<?php

namespace App\Controller\Backend;

use App\Entity\User;
use App\Form\AdminType;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;

class UserController extends AbstractController
{
    /**
     * Index all students.
     *
     * @return \Symfony\Component\HttpFoundation\Response - backend/index.html.twig
     */
    #[Route('/administration/etudiants', name: 'admin_students')]
    public function adminStudents(UserRepository $userRepository, ParameterBagInterface $parameterBag)
    {
        $students = $userRepository->findByRole('ROLE_STUDENT');

        return $this->render('backend/student/index.html.twig', [
            'students' => $students,
            'apiToken' => $parameterBag->get('x-internal-api-token'),
        ]);
    }

    /**
     * Index all companies.
     *
     * @param EntityManagerInterface $entityManager
     *
     * @return \Symfony\Component\HttpFoundation\Response - backend/index.html.twig
     */
    #[Route('/administration/entreprises', name: 'admin_companies')]
    public function adminCompanies(EntityManagerInterface $entityManager)
    {
        $repository = $entityManager->getRepository(User::class);
        $companies = $repository->findByRole('ROLE_COMPANY');

        return $this->render('backend/company/index.html.twig', [
            'companies' => $companies,
        ]);
    }

    /**
     * Allow to approve or disapprove a student registration.
     *
     * @param $id
     * @param EntityManagerInterface $entityManager
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    #[Route('/administration/etudiants/approuver/{id}', name: 'approve_students')]
    public function approveStudents($id, EntityManagerInterface $entityManager)
    {
        $repository = $entityManager->getRepository(User::class);
        $student = $repository->find($id);

        $student->setIsApproved(!$student->isApproved());
        $entityManager->persist($student);
        $entityManager->flush();

        return $this->redirectToRoute('admin_students');
    }

    /**
     * Allow to approve or disapprove a company registration.
     *
     * @param $id
     * @param EntityManagerInterface $entityManager
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    #[Route('/administration/entreprises/approuver/{id}', name: 'approve_companies')]
    public function approveCompanies($id, EntityManagerInterface $entityManager)
    {
        $repository = $entityManager->getRepository(User::class);
        $student = $repository->find($id);

        $student->setIsApproved(!$student->isApproved());
        $entityManager->persist($student);
        $entityManager->flush();

        return $this->redirectToRoute('admin_companies');
    }

    #[Route('/administration/admin', name: 'admin_admin_index', methods: ['GET'])]
    #[IsGranted('ROLE_SUPER_ADMIN')]
    public function indexAdmin(UserRepository $userRepository): Response
    {
        $superAdministrators = $userRepository->findByRole('ROLE_SUPER_ADMIN');
        $administrators = $userRepository->findByRole('ROLE_ADMIN');

        foreach ($administrators as $administrator) {
            $superAdministrators[] = $administrator;
        }

        return $this->render('backend/user/admins.html.twig', [
            'admins' => $superAdministrators,
        ]);
    }

    #[Route('/administration/admin/ajouter', name: 'admin_admin_add', methods: ['GET', 'POST'])]
    #[Route('/administration/admin/{id}', name: 'admin_admin_edit', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_SUPER_ADMIN')]
    public function addAdmin(User $admin = null, Request $request, UserPasswordHasherInterface $passwordHasher, EntityManagerInterface $entityManager): Response
    {
        if (null === $admin) {
            $admin = (new User())
                ->setIsApproved(true)
                ->setIsVerified(true)
                ->setProfileCompleted(true)
                ->setRegistrationDate(new \DateTime())
            ;
        }

        $form = $this->createForm(AdminType::class, $admin);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            if (!empty($admin->getPlainPassword())) {
                $admin->setPassword($passwordHasher->hashPassword($admin, $admin->getPlainPassword()));
            }

            $admin->setUpdatedAt(new \DateTimeImmutable());

            $entityManager->persist($admin);
            $entityManager->flush();

            $this->addFlash(
                type: 'success',
                message: 'L\'administrateur a bien été enregistré',
            );

            return $this->redirectToRoute('admin_admin_index');
        } elseif ($form->isSubmitted()) {
            $this->addFlash(
                type: 'error',
                message: 'Merci de corriger les erreurs',
            );
        }

        return $this->renderForm('backend/user/admin_handle.html.twig', [
            'admin' => $admin,
            'form' => $form,
        ]);
    }

    #[Route('/administration/admin/{id}/supprimer', name: 'admin_admin_delete', methods: ['POST'])]
    #[IsGranted('ROLE_SUPER_ADMIN')]
    public function delete(Request $request, User $user, UserRepository $userRepository): Response
    {
        if ($this->isCsrfTokenValid('delete'.$user->getId(), $request->request->get('_token'))) {
            if ($user !== $this->getUser()) {
                $userRepository->remove($user, true);

                $this->addFlash(
                    type: 'success',
                    message: 'L\'utilisateur a bien été supprimé',
                );
            }
        }

        return $this->redirectToRoute('admin_admin_index', [], Response::HTTP_SEE_OTHER);
    }
}
