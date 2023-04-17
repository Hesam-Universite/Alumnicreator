<?php

namespace App\Controller\Frontend;

use App\Entity\Parameter;
use App\Entity\User;
use App\Form\CompanyType;
use App\Form\StudentType;
use App\Repository\DirectoryPageRepository;
use App\Repository\ParameterRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use SymfonyCasts\Bundle\VerifyEmail\Exception\VerifyEmailExceptionInterface;
use SymfonyCasts\Bundle\VerifyEmail\VerifyEmailHelperInterface;

#[Route('/inscription', name: 'register_')]
class RegisterController extends AbstractController
{
    /**
     * Display the buttons who redirect to the good registration (student or company).
     *
     * @return \Symfony\Component\HttpFoundation\Response - frontend/index.html.twig
     */
    #[Route('/', name: 'index')]
    public function register()
    {
        if ($this->getUser() && $this->getUser()->isProfileCompleted()) {
            return $this->redirectToRoute('resume_all');
        }

        return $this->render('frontend/register/index.html.twig');
    }

    /**
     * Register a new student.
     *
     * @param Request                     $request
     * @param EntityManagerInterface      $entityManager
     * @param UserPasswordHasherInterface $passwordHasher
     * @param VerifyEmailHelperInterface  $verifyEmailHelper
     * @param MailerInterface             $mailer
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response - frontend/student.html.twig
     *
     * @throws \Symfony\Component\Mailer\Exception\TransportExceptionInterface
     */
    #[Route('/etudiant', name: 'student')]
    public function registerStudent(Request $request, EntityManagerInterface $entityManager, UserPasswordHasherInterface $passwordHasher, VerifyEmailHelperInterface $verifyEmailHelper, MailerInterface $mailer, DirectoryPageRepository $directoryPageRepository, ParameterRepository $parameterRepository)
    {
        if ($this->getUser() && $this->getUser()->isProfileCompleted()) {
            return $this->redirectToRoute('homepage');
        }

        $student = null !== $this->getUser() ? $this->getUser() : new User();

        if (null !== $token = $request->query->get('token')) {
            $directoryPage = $directoryPageRepository->findByToken($token);

            if (null !== $directoryPage) {
                $student->setName($directoryPage->getLastname())
                    ->setFirstname($directoryPage->getFirstname())
                    ->setClass($directoryPage->getClass())
                    ->setLinkedinLink($directoryPage->getLinkedinLink())
                ;

                $directoryPage->setUser($student);
                $entityManager->persist($directoryPage);
            }
        }

        $form = $this->createForm(StudentType::class, $student);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            if ($student->getClass() < idate('Y')) { // If the class (Promotion) is younger than the current date, so user is currently a student
                if ($student->getStatus() == 0) {
                    $student->setRoles(['ROLE_STUDENT', 'ROLE_ALUMNI', 'ROLE_IN_SEARCH']);
                } else {
                    $student->setRoles(['ROLE_STUDENT', 'ROLE_ALUMNI', 'ROLE_RECRUITER']);
                }
            } else {
                $student->setRoles(['ROLE_STUDENT', 'ROLE_CURRENT_STUDENT']);
            }
            $hashedPassword = $passwordHasher->hashPassword($student, $student->getPlainPassword());
            $student->setCity($request->request->get('student')['city']);
            $student->setPassword($hashedPassword);
            $student->setIsApproved(false);
            $student->setProfileCompleted(true);
            $student->setRegistrationDate(new \DateTime());
            $entityManager->persist($student);
            $entityManager->flush();

            if ($student->isVerified()) {
                $this->addFlash(
                    'success',
                    'Profil complété avec succès.',
                );
            } else {
                $this->addFlash(
                    'success',
                    'Inscription effectuée avec succès. Merci de confirmer votre mail.',
                );

                $signatureComponents = $verifyEmailHelper->generateSignature(
                    'register_app_verify_email',
                    $student->getId(),
                    $student->getEmail(),
                    ['id' => $student->getId()]
                );

                $confirmationUrl = $signatureComponents->getSignedUrl();
                $emailFrom = $parameterRepository->findOneBy(['code' => Parameter::SMTP_FROM])->getValue();
                $confirmationMail = (new Email())
                    ->from($emailFrom)
                    ->to($student->getEmail())
                    ->subject('Confirmez votre mail | Alumni Creator')
                    ->html('<h1>Confirmation</h1><p>Pour vous inscrire sur notre plateforme, vous devez cliquer sur le lien ci-dessous : </p><a href="'.$confirmationUrl.'" target="_blank">'.$confirmationUrl.'</a>');

                $mailer->send($confirmationMail);
            }

            return $this->redirectToRoute('app_login');
        }

        return $this->render('frontend/register/student.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * Register a new company.
     *
     * @param Request                     $request
     * @param EntityManagerInterface      $entityManager
     * @param UserPasswordHasherInterface $passwordHasher
     * @param VerifyEmailHelperInterface  $verifyEmailHelper
     * @param MailerInterface             $mailer
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response - frontend/company.html.twig
     *
     * @throws \Symfony\Component\Mailer\Exception\TransportExceptionInterface
     */
    #[Route('/entreprise', name: 'company')]
    public function registerCompany(Request $request, EntityManagerInterface $entityManager, UserPasswordHasherInterface $passwordHasher, VerifyEmailHelperInterface $verifyEmailHelper, MailerInterface $mailer, ParameterRepository $parameterRepository)
    {
        if ($this->getUser() && $this->getUser()->isProfileCompleted()) {
            return $this->redirectToRoute('homepage');
        }

        $company = null !== $this->getUser() ? $this->getUser() : new User();
        $form = $this->createForm(CompanyType::class, $company);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $company->setRoles(['ROLE_COMPANY']);
            $hashedPassword = $passwordHasher->hashPassword($company, $company->getPlainPassword());
            $company->setPassword($hashedPassword);
            $company->setIsApproved(false);
            $company->setProfileCompleted(true);
            $company->setRegistrationDate(new \DateTime());
            $entityManager->persist($company);
            $entityManager->flush();

            if ($company->isVerified()) {
                $this->addFlash(
                    'success',
                    'Profil complété avec succès.',
                );
            } else {
                $signatureComponents = $verifyEmailHelper->generateSignature(
                    'register_app_verify_email',
                    $company->getId(),
                    $company->getEmail(),
                    ['id' => $company->getId()]
                );

                $this->addFlash(
                    'success',
                    'Inscription effectuée avec succès. Merci de confirmer votre mail.',
                );

                $confirmationUrl = $signatureComponents->getSignedUrl();
                $emailFrom = $parameterRepository->findOneBy(['code' => Parameter::SMTP_FROM])->getValue();
                $confirmationMail = (new Email())
                    ->from($emailFrom)
                    ->to($company->getEmail())
                    ->subject('Confirmez votre mail | Alumni Creator')
                    ->html('<h1>Confirmation</h1><p>Pour vous inscrire sur notre plateforme, vous devez cliquer sur le lien ci-dessous : </p><a href="'.$confirmationUrl.'" target="_blank">'.$confirmationUrl.'</a>');

                $mailer->send($confirmationMail);
            }

            return $this->redirectToRoute('app_login');
        }

        return $this->render('frontend/register/company.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * Check the link sent by mail to confirm user registration.
     *
     * @param Request                    $request
     * @param VerifyEmailHelperInterface $verifyEmailHelper
     * @param UserRepository             $userRepository
     * @param EntityManagerInterface     $entityManager
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    #[Route('/verify', name: 'app_verify_email')]
    public function verifyUserEmail(Request $request, VerifyEmailHelperInterface $verifyEmailHelper, UserRepository $userRepository, EntityManagerInterface $entityManager)
    {
        $user = $userRepository->find($request->query->get('id'));
        if (!$user) {
            throw $this->createNotFoundException();
        }

        try {
            $verifyEmailHelper->validateEmailConfirmation(
                $request->getUri(),
                $user->getId(),
                $user->getEmail()
            );
        } catch (VerifyEmailExceptionInterface $e) {
            $this->addFlash('error', $e->getReason());

            return $this->redirectToRoute('register_index');
        }

        $user->setIsVerified(true);
        $entityManager->flush();

        $this->addFlash('success', 'Votre mail a été vérifié !');

        return $this->redirectToRoute('app_login');
    }
}
