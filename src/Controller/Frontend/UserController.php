<?php

namespace App\Controller\Frontend;

use App\Entity\User;
use App\Entity\UserInstance;
use App\Form\AdminAccountType;
use App\Form\CompanyType;
use App\Form\StudentType;
use App\Repository\InstanceRepository;
use App\Repository\JobInstanceRepository;
use App\Repository\JobRepository;
use App\Repository\ResumeInstanceRepository;
use App\Repository\ResumeRepository;
use App\Repository\UserInstanceRepository;
use App\Repository\UserRepository;
use App\Service\RandomProfilesService;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;

class UserController extends AbstractController
{
    #[Route('/utilisateur/{id<\d+>}', name: 'one_user')]
    #[Route('/mon-compte', name: 'my_account')]
    #[Security("is_granted('ROLE_STUDENT') or is_granted('ROLE_COMPANY') or is_granted('ROLE_ADMIN')")]
    public function myAccount(User $user = null, ResumeRepository $resumeRepository, UserRepository $userRepository, JobRepository $jobRepository, RandomProfilesService $randomProfilesService, EntityManagerInterface $entityManager): Response
    {
        if (null === $user) {
            $user = $this->getUser();

            $user->setLastConnection(new \DateTime());
            $entityManager->persist($user);
            $entityManager->flush();
        }

        $resume = $resumeRepository->findOneBy(['user' => $user]);

        $jobs = null;
        if (in_array('ROLE_COMPANY', $user->getRoles()) || in_array('ROLE_ALUMNI', $user->getRoles())) {
            $jobs = $jobRepository->findByAuthor($user);
        }

        // Searching similar resumes and companies ID
        if (!in_array('ROLE_COMPANY', $user->getRoles()) && null != $resume && null != $resume->getActivityArea()) {
            $similarProfilesId = $resumeRepository->findAllIdWithActivityArea($resume->getActivityArea(), $this->getUser());
            if (null == $similarProfilesId) {
                $similarProfilesId = $resumeRepository->findAllIdExceptConnectedUser($this->getUser());
            }
            $suggestedCompaniesId = $userRepository->findCompaniesIdWithActivityArea($resume->getActivityArea());
            if (null == $suggestedCompaniesId) {
                $suggestedCompaniesId = $userRepository->findAllIdByRoleAndApproved('ROLE_COMPANY');
            }
        } else {
            $similarProfilesId = $resumeRepository->findAllIdExceptConnectedUser($this->getUser());
            $suggestedCompaniesId = $userRepository->findAllIdByRoleAndApproved('ROLE_COMPANY');
        }

        return $this->render('frontend/user/one.html.twig', [
            'user' => $user,
            'resume' => $resume,
            'similarProfiles' => $randomProfilesService->getRandomUsers($similarProfilesId),
            'suggestedCompanies' => $randomProfilesService->getRandomCompanies($suggestedCompaniesId),
            'jobs' => $jobs,
        ]);
    }

    #[Route('/utilisateur/i/{id<\d+>}', name: 'one_user_instance')]
    #[Security("is_granted('ROLE_STUDENT') or is_granted('ROLE_COMPANY') or is_granted('ROLE_ADMIN')")]
    public function oneAccountInstance(UserInstance $user, ResumeInstanceRepository $resumeInstanceRepository, UserRepository $userRepository, JobInstanceRepository $jobInstanceRepository, RandomProfilesService $randomProfilesService, ResumeRepository $resumeRepository, InstanceRepository $instanceRepository): Response
    {
        $resume = $resumeInstanceRepository->findOneBy(['userId' => $user->getOtherInstanceId(), 'instanceId' => $user->getInstanceId()]);
        $instance = $instanceRepository->findOneBy(['externalId' => $user->getInstanceId()]);

        $jobs = null;
        if (in_array('ROLE_COMPANY', $user->getRoles()) || in_array('ROLE_ALUMNI', $user->getRoles())) {
            $jobs = $jobInstanceRepository->findBy(['authorId' => $user->getOtherInstanceId(), 'instanceId' => $user->getInstanceId()]);
        }

        // Searching similar resumes and companies ID
        if (!in_array('ROLE_COMPANY', $user->getRoles()) && null != $resume && null != $resume->getActivityArea()) {
            $similarProfilesId = $resumeRepository->findAllIdWithActivityArea($resume->getActivityArea(), $this->getUser());
            if (null == $similarProfilesId) {
                $similarProfilesId = $resumeRepository->findAllIdExceptConnectedUser($this->getUser());
            }
            $suggestedCompaniesId = $userRepository->findCompaniesIdWithActivityArea($resume->getActivityArea());
            if (null == $suggestedCompaniesId) {
                $suggestedCompaniesId = $userRepository->findAllIdByRoleAndApproved('ROLE_COMPANY');
            }
        } else {
            $similarProfilesId = $resumeRepository->findAllIdExceptConnectedUser($this->getUser());
            $suggestedCompaniesId = $userRepository->findAllIdByRoleAndApproved('ROLE_COMPANY');
        }

        return $this->render('frontend/user/one_instance.html.twig', [
            'user' => $user,
            'resume' => $resume,
            'similarProfiles' => $randomProfilesService->getRandomUsers($similarProfilesId),
            'suggestedCompanies' => $randomProfilesService->getRandomCompanies($suggestedCompaniesId),
            'jobs' => $jobs,
            'instance' => $instance,
        ]);
    }

    /**
     * page to edit the connected user's account.
     *
     * @param Request                     $request
     * @param EntityManagerInterface      $entityManager
     * @param UserPasswordHasherInterface $passwordHasher
     *
     * @return Response - frontend/user/edit.html.twig
     */
    #[Route('/modifier-mon-compte', name: 'edit_my_account')]
    #[Security("is_granted('ROLE_STUDENT') or is_granted('ROLE_COMPANY') or is_granted('ROLE_ADMIN')")]
    public function edit(Request $request, EntityManagerInterface $entityManager, UserPasswordHasherInterface $passwordHasher): Response
    {
        $user = $this->getUser();

        if ($this->isGranted('ROLE_ADMIN')) {
            $form = $this->createForm(AdminAccountType::class, $user);
        } elseif (in_array('ROLE_COMPANY', $user->getRoles())) {
            $form = $this->createForm(CompanyType::class, $user);
        } else {
            $form = $this->createForm(StudentType::class, $user);
        }
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            if (null !== $form->getData()->getPlainPassword()) {
                $encodedPassword = $passwordHasher->hashPassword(
                    $user,
                    $form->getData()->getPlainPassword()
                );
                $user->setPassword($encodedPassword);
            }
            $entityManager->persist($user);
            $entityManager->flush();

            $this->addFlash(
                'success',
                'Votre compte a été mis à jour.',
            );

            return $this->redirectToRoute('my_account');
        }

        return $this->render('frontend/user/edit.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * Display all the companies in the front.
     *
     * @param UserRepository $userRepository
     *
     * @return \Symfony\Component\HttpFoundation\Response - frontend/index.html.twig
     */
    #[Route('/espace-carriere/entreprises', name: 'index_companies')]
    #[Security("is_granted('ROLE_STUDENT') or is_granted('ROLE_ADMIN')")]
    public function indexCompanies(UserRepository $userRepository, UserInstanceRepository $userInstanceRepository)
    {
        $companies = $userRepository->findByRoleAndApproved('ROLE_COMPANY');
        $companiesOtherInstances = $userInstanceRepository->findByRole('ROLE_COMPANY');

        foreach ($companiesOtherInstances as $companyOtherInstance) {
            $companies[] = $companyOtherInstance;
        }

        usort($companies, function ($a, $b) {
            return strcmp($a->getCompanyName(), $b->getCompanyName());
        });

        return $this->render('frontend/company/index.html.twig', [
            'companies' => $companies,
        ]);
    }
}
