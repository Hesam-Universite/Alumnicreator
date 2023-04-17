<?php

namespace App\Controller\Backend;

use App\Entity\DirectoryPage;
use App\Form\DirectoryPageType;
use App\Form\UploadDirectoryType;
use App\Repository\DirectoryPageRepository;
use App\Repository\UserRepository;
use App\Service\DirectoryPageService;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/administration/annuaire', name: 'admin_directory_')]
#[Security("is_granted('ROLE_ADMIN')")]
class DirectoryController extends AbstractController
{
    /**
     * Index of all directory pages.
     *
     * @param DirectoryPageRepository $directoryPageRepository
     *
     * @return Response - template backend/directory/index.html.twig
     */
    #[Route('', name: 'index')]
    public function index(DirectoryPageRepository $directoryPageRepository): Response
    {
        return $this->render('backend/directory/index.html.twig', [
            'directoryPages' => $directoryPageRepository->findBy([], ['lastname' => 'ASC']),
        ]);
    }

    /**
     * To edit or create a directory page from backoffice.
     *
     * @param DirectoryPage|null      $directoryPage           - The directory page id to edit
     * @param EntityManagerInterface  $entityManager
     * @param Request                 $request
     * @param UserRepository          $userRepository
     * @param DirectoryPageRepository $directoryPageRepository
     *
     * @return Response - template backend/directory/new.html.twig
     */
    #[Route('/nouveau', name: 'new')]
    #[Route('/modifier/{id<\d+>}', name: 'edit')]
    public function handle(DirectoryPage $directoryPage = null, EntityManagerInterface $entityManager, Request $request, UserRepository $userRepository, DirectoryPageRepository $directoryPageRepository): Response
    {
        if (null === $directoryPage) {
            $directoryPage = new DirectoryPage();
        }

        $form = $this->createForm(DirectoryPageType::class, $directoryPage);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();
            $userLinkedToTheEmail = $userRepository->findOneBy(['email' => $data->getEmail()]);
            $directoryPage->setUser($userLinkedToTheEmail);

            $entityManager->persist($directoryPage);
            $entityManager->flush();

            $this->addFlash(
                'success',
                'Page d\'annuaire enregistrée',
            );

            return $this->redirectToRoute('admin_directory_index');
        }

        return $this->render('backend/directory/new.html.twig', [
            'form' => $form->createView(),
            'directoryPage' => $directoryPage,
        ]);
    }

    /**
     * Delete a directory page.
     *
     * @param DirectoryPage          $directoryPage - The directory page to delete
     * @param EntityManagerInterface $entityManager
     *
     * @return RedirectResponse - redirects to the index page
     */
    #[Route('/{id<\d+>}/supprimer', name: 'delete')]
    public function delete(DirectoryPage $directoryPage, EntityManagerInterface $entityManager): RedirectResponse
    {
        $entityManager->remove($directoryPage);
        $entityManager->flush();

        $this->addFlash(
            'success',
            'Page d\'annuaire supprimée avec succès',
        );

        return $this->redirectToRoute('admin_directory_index');
    }

    /**
     * Import a CSV or XLSX file to upload directory data.
     *
     * @param Request              $request
     * @param DirectoryPageService $directoryPageService
     *
     * @return Response - remplate backend/directory/import.html.twig
     */
    #[Route('/import', name: 'import', methods: ['GET', 'POST'])]
    public function import(Request $request, DirectoryPageService $directoryPageService): Response
    {
        $form = $this->createForm(UploadDirectoryType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            $file = $form->get('directory')->getData();
            $directoryPageService->import($file);

            $this->addFlash(
                type: 'success',
                message: 'Import effectué avec succès',
            );

            return $this->redirectToRoute('admin_directory_index');
        }

        return $this->renderForm('backend/directory/import.html.twig', [
            'form' => $form,
        ]);
    }
}
