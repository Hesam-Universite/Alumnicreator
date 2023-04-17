<?php

namespace App\Controller\Frontend;

use App\Entity\DirectoryPage;
use App\Form\DirectoryPageFrontType;
use App\Form\LostFromSightType;
use App\Form\SearchDirectoryPageType;
use App\Repository\DirectoryPageInstanceRepository;
use App\Repository\DirectoryPageRepository;
use App\Service\EmailService;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/annuaire', name: 'directory_')]
class DirectoryController extends AbstractController
{
    /**
     * Index all the directory pages.
     *
     * @param DirectoryPageRepository $directoryPageRepository
     * @param Request                 $request
     *
     * @return Response - template frontend/directory/index.html.twig
     */
    #[Security("is_granted('ROLE_STUDENT') or is_granted('ROLE_COMPANY') or is_granted('ROLE_ADMIN')")]
    #[Route('', name: 'index')]
    public function index(DirectoryPageRepository $directoryPageRepository, Request $request, DirectoryPageInstanceRepository $directoryPageInstanceRepository): Response
    {
        $form = $this->createForm(SearchDirectoryPageType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();
            $directoryPages = $directoryPageRepository->findByFilters($data['keyword'], $data['class']);
            $directoryPagesOtherInstances = $directoryPageInstanceRepository->findByFilters($data['keyword'], $data['class']);
        } else {
            $directoryPages = $directoryPageRepository->findAll();
            $directoryPagesOtherInstances = $directoryPageInstanceRepository->findAll();
        }

        foreach ($directoryPagesOtherInstances as $directoryPageOtherInstance) {
            $directoryPages[] = $directoryPageOtherInstance;
        }

        usort($directoryPages, function ($a, $b) {
            return strcmp($a->getLastname(), $b->getLastname());
        });

        return $this->render('frontend/directory/index.html.twig', [
            'directoryPages' => $directoryPages,
            'form' => $form->createView(),
        ]);
    }

    /**
     * To edit or create his own page.
     *
     * @param DirectoryPageRepository $directoryPageRepository
     * @param Request                 $request
     * @param EntityManagerInterface  $entityManager
     *
     * @return Response - frontend/directory/my_page.html.twig
     */
    #[Security("is_granted('ROLE_STUDENT')")]
    #[Route('/ma-page', name: 'my_page')]
    public function edit(DirectoryPageRepository $directoryPageRepository, Request $request, EntityManagerInterface $entityManager): Response
    {
        $directoryPage = $directoryPageRepository->findOneBy(['user' => $this->getUser()]);

        if ($directoryPage === null) {
            $directoryPage = new DirectoryPage();
            $firstName = $this->getUser()->getFirstname();
            $lastName = $this->getUser()->getName();
        } else {
            $firstName = $directoryPage->getFirstname();
            $lastName = $directoryPage->getLastname();
        }

        $form = $this->createForm(DirectoryPageFrontType::class, $directoryPage);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $directoryPage->setUser($this->getUser());
            $entityManager->persist($directoryPage);
            $entityManager->flush();

            $this->addFlash(
                'success',
                'Page d\'annuaire enregistrée',
            );

            return $this->redirectToRoute('directory_index');
        }

        return $this->render('frontend/directory/my_page.html.twig', [
            'form' => $form->createView(),
            'directoryPage' => $directoryPage,
            'firstName' => $firstName,
            'lastName' => $lastName,
        ]);
    }

    /**
     * Display lost from sight form.
     *
     * @param Request      $request
     * @param EmailService $emailService
     *
     * @return Response - template frontend/directory/lost_from_sight.html.twig
     */
    #[Security("is_granted('ROLE_STUDENT') or is_granted('ROLE_ADMIN')")]
    #[Route('/perdu-de-vue', name: 'lost_from_sight')]
    public function lostFromSight(Request $request, EmailService $emailService): Response
    {
        $form = $this->createForm(LostFromSightType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();

            try {
                $emailService->perduDeVue($data);

                $this->addFlash(
                    type: 'success',
                    message: 'Votre demande a bien été transmise à l\'administrateur.',
                );
            } catch (TransportExceptionInterface $e) {
                $this->addFlash(
                    type : 'danger',
                    message: 'Une erreur s\'est produite, merci de réessayer dans quelques instants',
                );
            }

            return $this->redirectToRoute('directory_index');
        }

        return $this->renderForm('frontend/directory/lost_from_sight.html.twig', [
            'form' => $form,
        ]);
    }
}
