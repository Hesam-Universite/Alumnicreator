<?php

namespace App\Controller\Backend;

use App\Entity\Page;
use App\Form\PageType;
use App\Repository\PageRepository;
use App\Service\AttachmentManagerService;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\AsciiSlugger;

#[Route('/administration/pages', name: 'page_admin_')]
class PageController extends AbstractController
{
    /**
     * index all pages.
     *
     * @param PageRepository $pageRepository
     *
     * @return Response - backend/page/index.html.twig
     */
    #[Security('is_granted("ROLE_ADMIN")')]
    #[Route('/', name: 'index')]
    public function index(PageRepository $pageRepository): Response
    {
        $pages = $pageRepository->findBy([], ['id' => 'DESC']);

        return $this->render('backend/page/index.html.twig', [
            'pages' => $pages,
        ]);
    }

    /**
     * Create a new page.
     *
     * @param Page|null              $page
     * @param Request                $request
     * @param EntityManagerInterface $entityManager
     * @param PageRepository         $pageRepository
     *
     * @return RedirectResponse|Response - backend/page/new.html.twig
     */
    #[Security('is_granted("ROLE_ADMIN")')]
    #[Route('/nouvelle', name: 'new')]
    #[Route('/modifier/{id<\d+>}', name: 'edit')]
    public function handle(Page $page = null, Request $request, EntityManagerInterface $entityManager, PageRepository $pageRepository)
    {
        $slugger = new AsciiSlugger();

        if (null === $page) {
            $page = new Page();
        }

        $form = $this->createForm(PageType::class, $page);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $slugWroteByUser = $form->getData()->getSlug();
            if (null !== $slugWroteByUser) {
                $slug = strtolower($slugger->slug($slugWroteByUser));
            } else {
                $slug = strtolower($slugger->slug($form->getData()->getTitle()));
            }
            $pageWithSameSlug = $pageRepository->findOneBy(['slug' => $slug]);

            // The slug has to be unique
            if (null === $pageWithSameSlug || $pageWithSameSlug->getId() === $page->getId()) {
                $page->setSlug($slug);
                $entityManager->persist($page);
                $entityManager->flush();

                $this->addFlash(
                    'success',
                    'Page enregistré avec succès'
                );
            } else {
                $this->addFlash(
                    'error',
                    'Le slug ou le titre de cette page existe déjà'
                );
            }

            return $this->redirectToRoute('page_admin_index');
        }

        return $this->render('backend/page/new.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * Delete a page.
     *
     * @param Page                   $page
     * @param EntityManagerInterface $entityManager
     *
     * @return RedirectResponse
     */
    #[Security('is_granted("ROLE_ADMIN")')]
    #[Route('/supprimer/{id<\d+>}', name: 'delete')]
    public function delete(Page $page, EntityManagerInterface $entityManager): RedirectResponse
    {
        $entityManager->remove($page);
        $entityManager->flush();

        $this->addFlash(
            'success',
            'Page supprimée avec succès',
        );

        return $this->redirectToRoute('page_admin_index');
    }

    #[Route('/attachment-page', name: 'attachment')]
    public function uploadImageViaTinyMce(Request $request, AttachmentManagerService $attachmentManagerService, PageRepository $pageRepository)
    {
        $file = $request->files->get('file');

        $fileNameAndPath = $attachmentManagerService->uploadAttachment($file);

        return $this->json([
            'location' => $fileNameAndPath['path'],
        ]);
    }
}
