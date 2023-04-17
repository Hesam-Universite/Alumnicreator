<?php

namespace App\Controller\Backend;

use App\Entity\Article;
use App\Form\ArticleType;
use App\Repository\ArticleRepository;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\AsciiSlugger;

#[Security('is_granted("ROLE_ADMIN")')]
#[Route('/administration/articles/', name: 'article_admin_')]
class ArticleController extends AbstractController
{
    /**
     * Index all aticles in the backoffice.
     *
     * @param ArticleRepository $articleRepository
     *
     * @return Response - backend/article/index.html.twig
     */
    #[Route('', name: 'index')]
    public function index(ArticleRepository $articleRepository): Response
    {
        $articles = $articleRepository->findBy([], ['publishedAt' => 'DESC']);

        return $this->render('backend/article/index.html.twig', [
            'articles' => $articles,
        ]);
    }

    /**
     * Edit or create an article.
     *
     * @param Article|null           $article
     * @param Request                $request
     * @param EntityManagerInterface $entityManager
     * @param ArticleRepository      $articleRepository
     *
     * @return RedirectResponse|Response - backend/article/new.html.twig
     */
    #[Route('nouveau', name: 'new')]
    #[Route('modifier/{id<\d+>}', name: 'edit')]
    public function handle(Article $article = null, Request $request, EntityManagerInterface $entityManager, ArticleRepository $articleRepository)
    {
        $slugger = new AsciiSlugger();

        if (null === $article) {
            $article = new Article();
        }

        $form = $this->createForm(ArticleType::class, $article);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $slug = strtolower($slugger->slug($form->getData()->getTitle()));
            $articleWithSameSlug = $articleRepository->findOneBy(['slug' => $slug]);

            // The slug has to be unique
            if (null === $articleWithSameSlug || $articleWithSameSlug->getId() === $article->getId()) {
                $article->setSlug($slug);
                $entityManager->persist($article);
                $entityManager->flush();

                $this->addFlash(
                    'success',
                    'Article enregistré avec succès'
                );
            } else {
                $this->addFlash(
                    'error',
                    'Ce titre d\'article existe déjà'
                );
            }

            return $this->redirectToRoute('article_admin_index');
        }

        return $this->render('backend/article/new.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * Delete an article.
     *
     * @param Article                $article
     * @param EntityManagerInterface $entityManager
     *
     * @return RedirectResponse
     */
    #[Route('supprimer/{id<\d+>}', name: 'delete')]
    public function delete(Article $article, EntityManagerInterface $entityManager): RedirectResponse
    {
        $entityManager->remove($article);
        $entityManager->flush();

        $this->addFlash(
            'success',
            'Article supprimé avec succès',
        );

        return $this->redirectToRoute('article_admin_index');
    }
}
