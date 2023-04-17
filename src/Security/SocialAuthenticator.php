<?php

namespace App\Security;

use App\Entity\User;
use App\Repository\DirectoryPageRepository;
use App\Repository\ParameterRepository;
use Doctrine\ORM\EntityManagerInterface;
use KnpU\OAuth2ClientBundle\Client\ClientRegistry;
use KnpU\OAuth2ClientBundle\Security\Authenticator\OAuth2Authenticator;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\Exception\CustomUserMessageAuthenticationException;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\UserBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Passport;
use Symfony\Component\Security\Http\Authenticator\Passport\SelfValidatingPassport;
use Symfony\Component\Security\Http\EntryPoint\AuthenticationEntryPointInterface;

class SocialAuthenticator extends OAuth2Authenticator implements AuthenticationEntryPointInterface
{
    public function __construct(
        private ClientRegistry $clientRegistry,
        private EntityManagerInterface $entityManager,
        private RouterInterface $router,
        private DirectoryPageRepository $directoryPageRepository,
        private ParameterRepository $parameterRepository,
        private FlashBagInterface $flashBag,
    ) {
    }

    public function supports(Request $request): ?bool
    {
        // continue ONLY if the current ROUTE matches the check ROUTE
        return $request->attributes->get('_route') === 'oauth_check';
    }

    public function authenticate(Request $request): Passport
    {
        $service = $request->get('service');
        $client = $this->clientRegistry->getClient($service);
        $accessToken = $this->fetchAccessToken($client);

        return new SelfValidatingPassport(
            new UserBadge($accessToken->getToken(), function () use ($accessToken, $client, $service) {
                $socialUser = $client->fetchUserFromToken($accessToken);
                $email = $socialUser->getEmail();

                $socialId = $service.'Id'; // googleId, linkedinId, microsoftId

                if ('facebook' === $service) {
                    if (null == $this->parameterRepository->findOneBy(['code' => 'FAPI'])->getValue()) {
                        $facebookId = $this->parameterRepository->findOneBy(['code' => 'FAPI'])->setValue($socialUser->getId());
                        $facebookToken = $this->parameterRepository->findOneBy(['code' => 'FBTO'])->setValue($accessToken->getToken());
                        $this->entityManager->persist($facebookId);
                        $this->entityManager->persist($facebookToken);
                        $this->entityManager->flush();

                        throw new CustomUserMessageAuthenticationException('Le compte Facebook a bien été associé.');
                    } else {
                        throw new CustomUserMessageAuthenticationException('Un compte Facebook est déjà associé.');
                    }
                }

                $existingUser = $this->entityManager->getRepository(User::class)->findOneBy([$socialId => $socialUser->getId()]);

                if (null !== $existingUser) {
                    if ($existingUser->isProfileCompleted() && !$existingUser->isApproved()) {
                        throw new CustomUserMessageAuthenticationException('Votre compte doit être activé par l\'administrateur.');
                    }

                    return $existingUser;
                }

                $user = $this->entityManager->getRepository(User::class)->findOneBy(['email' => $email]);

                if (null !== $user) {
                    if ($user->isProfileCompleted() && !$user->isApproved()) {
                        throw new CustomUserMessageAuthenticationException('Votre compte doit être activé par l\'administrateur.');
                    }

                    $method = 'set'.ucfirst($service).'Id';
                    $user->$method($socialUser->getId());
                    $this->entityManager->flush();

                    return $user;
                }

                $user = (new User())
                    ->setEmail($socialUser->getEmail())
                    ->setFirstname($socialUser->getFirstName())
                    ->setName($socialUser->getLastName())
                    ->setProfileCompleted(false)
                    ->setIsApproved(false)
                    ->setRegistrationDate(new \DateTime())
                    ->setIsVerified(true);

                $method = 'set'.ucfirst($service).'Id';
                $user->$method($socialUser->getId());

                $this->entityManager->persist($user);
                $this->entityManager->flush();

                return $user;
            })
        );
    }

    public function onAuthenticationSuccess(Request $request, TokenInterface $token, string $firewallName): ?Response
    {
        $user = $token->getUser();

        if (!$user->isProfileCompleted()) {
            $directoryPage = $this->directoryPageRepository->findOneBy(['email' => $user->getEmail()]);

            if (null !== $directoryPage) {
                return new RedirectResponse($this->router->generate('register_student', ['token' => md5($directoryPage->getId())]));
            }

            return new RedirectResponse($this->router->generate('register_index'));
        }

        return new RedirectResponse($this->router->generate('homepage'));
    }

    public function onAuthenticationFailure(Request $request, AuthenticationException $exception): ?Response
    {
        $message = strtr($exception->getMessageKey(), $exception->getMessageData());

        if (str_contains($message, 'Facebook')) {
            $type = $message == 'Un compte Facebook est déjà associé.' ? 'error' : 'success';
            $this->flashBag->add($type, $message);

            return new RedirectResponse($this->router->generate('admin_social_posts_index'));
        }

        return new Response($message, Response::HTTP_FORBIDDEN);
    }

    /**
     * Called when authentication is needed, but it's not sent.
     * This redirects to the 'login'.
     */
    public function start(Request $request, AuthenticationException $authException = null): Response
    {
        return new RedirectResponse(
            '/login', // might be the site, where users choose their oauth provider
            Response::HTTP_TEMPORARY_REDIRECT
        );
    }
}
