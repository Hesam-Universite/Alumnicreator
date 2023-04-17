<?php

namespace App\Controller;

use KnpU\OAuth2ClientBundle\Client\ClientRegistry;
use KnpU\OAuth2ClientBundle\Client\Provider\FacebookClient;
use KnpU\OAuth2ClientBundle\Client\Provider\GoogleClient;
use KnpU\OAuth2ClientBundle\Client\Provider\LinkedInClient;
use KnpU\OAuth2ClientBundle\Client\Provider\MicrosoftClient;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class SecurityController extends AbstractController
{
    /**
     * Login form.
     *
     * @param AuthenticationUtils $authenticationUtils
     *
     * @return Response - security/login.html.twig - security/login.html.twig
     */
    #[Route(path: '/connexion', name: 'app_login')]
    public function login(AuthenticationUtils $authenticationUtils): Response
    {
        if ($this->getUser()) {
            return $this->redirectToRoute('my_account');
        }

        // get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();
        // last username entered by the user
        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render('security/login.html.twig', ['last_username' => $lastUsername, 'error' => $error]);
    }

    /**
     * Auth with Google.
     *
     * @param ClientRegistry $clientRegistry
     *
     * @return RedirectResponse
     */
    #[Route(path: '/connect/google', name: 'google_connect')]
    public function connectGoogle(ClientRegistry $clientRegistry): RedirectResponse
    {
        /** @var GoogleClient $client */
        $client = $clientRegistry->getClient('google');

        return $client->redirect(['https://www.googleapis.com/auth/userinfo.profile', 'https://www.googleapis.com/auth/userinfo.email']);
    }

    /**
     * Auth with LinkedIn.
     *
     * @param ClientRegistry $clientRegistry
     *
     * @return RedirectResponse
     */
    #[Route(path: '/connect/linkedin', name: 'linkedin_connect')]
    public function connectLinkedin(ClientRegistry $clientRegistry): RedirectResponse
    {
        /** @var LinkedInClient $client */
        $client = $clientRegistry->getClient('linkedin');

        return $client->redirect(['r_liteprofile', 'r_emailaddress']);
    }

    /**
     * Auth with Microsoft.
     *
     * @param ClientRegistry $clientRegistry
     *
     * @return RedirectResponse
     */
    #[Route(path: '/connect/microsoft', name: 'microsoft_connect')]
    public function connectMicrosoft(ClientRegistry $clientRegistry): RedirectResponse
    {
        /** @var MicrosoftClient $client */
        $client = $clientRegistry->getClient('microsoft');

        return $client->redirect(['wl.basic', 'wl.signin', 'wl.emails']);
    }

    /**
     * Auth with Facebook.
     *
     * @param ClientRegistry $clientRegistry
     *
     * @return RedirectResponse
     */
    #[Route(path: '/connect/facebook', name: 'facebook_connect')]
    public function connectFacebook(ClientRegistry $clientRegistry): RedirectResponse
    {
        /** @var FacebookClient $client */
        $client = $clientRegistry->getClient('facebook');

        return $client->redirect(['email', 'pages_show_list', 'pages_read_engagement', 'pages_manage_metadata', 'pages_read_user_content', 'pages_manage_engagement', 'public_profile', 'pages_manage_posts']);
    }

    /**
     * Logout.
     *
     * @return void
     */
    #[Route(path: '/deconnexion', name: 'app_logout')]
    public function logout(): void
    {
        throw new \LogicException('This method can be blank - it will be intercepted by the logout key on your firewall.');
    }

    /**
     * @param string $service
     *
     * @return void
     */
    #[Route('/oauth/callbacks/{service}', name: 'oauth_check', schemes: ['https'])]
    public function registerOAuth(string $service)
    {
        // Redirects to service authenticator
    }
}
