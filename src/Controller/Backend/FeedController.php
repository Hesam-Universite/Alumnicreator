<?php

namespace App\Controller\Backend;

use App\Entity\Feed;
use App\Form\FeedType;
use App\Repository\FeedRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Cache\CacheInterface;

#[Route('/administration/feeds', name: 'admin_feed_')]
class FeedController extends AbstractController
{
    /**
     * Display the list of feeds.
     *
     * @param FeedRepository $feedRepository
     *
     * @return Response - template backend/feed/index.html.twig
     */
    #[Route('/', name: 'index', methods: ['GET'])]
    public function index(FeedRepository $feedRepository): Response
    {
        return $this->render('backend/feed/index.html.twig', [
            'feeds' => $feedRepository->findAll(),
        ]);
    }

    /**
     * Add or edit a feed.
     *
     * @param Feed|null      $feed           - The feed's id
     * @param Request        $request
     * @param FeedRepository $feedRepository
     *
     * @return Response - template backend/feed/handle.html.twig or redirects to admin_feed_index
     */
    #[Route('/ajouter', name: 'new', methods: ['GET', 'POST'])]
    #[Route('/{id}', name: 'edit', methods: ['GET', 'POST'])]
    public function handle(Feed $feed = null, Request $request, FeedRepository $feedRepository, CacheInterface $cache): Response
    {
        if (null === $feed) {
            $feed = new Feed();
        }

        $form = $this->createForm(FeedType::class, $feed);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $feedRepository->add($feed, true);

            $this->addFlash(
                type: 'success',
                message: 'Le flux a bien été '.($request->get('_route') === 'admin_feed_new' ? 'ajouté' : 'modifié'),
            );

            $cache->delete('external_feeds');

            return $this->redirectToRoute('admin_feed_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('backend/feed/handle.html.twig', [
            'feed' => $feed,
            'form' => $form,
        ]);
    }

    /**
     * Deletes a feed.
     *
     * @param Request        $request
     * @param Feed           $feed
     * @param FeedRepository $feedRepository
     *
     * @return Response - redirects to admin_feed_index
     */
    #[Route('/{id}/supprimer', name: 'delete', methods: ['POST'])]
    public function delete(Request $request, Feed $feed, FeedRepository $feedRepository, CacheInterface $cache): Response
    {
        if ($this->isCsrfTokenValid('delete'.$feed->getId(), $request->request->get('_token'))) {
            $feedRepository->remove($feed, true);

            $this->addFlash(
                type: 'success',
                message: 'Le flux a bien été supprimé',
            );

            $cache->delete('external_feeds');
        }

        return $this->redirectToRoute('admin_feed_index', [], Response::HTTP_SEE_OTHER);
    }
}
