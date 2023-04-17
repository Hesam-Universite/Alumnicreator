<?php

namespace App\Controller\Frontend;

use App\Entity\Article;
use App\Entity\Parameter;
use App\Repository\ArticleRepository;
use App\Repository\ParameterRepository;
use App\Service\FeedService;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/articles/', name: 'article_')]
class ArticleController extends AbstractController
{
    /**
     * Index all articles in the front.
     *
     * @param ArticleRepository   $articleRepository
     * @param FeedService         $feedService
     * @param PaginatorInterface  $paginator
     * @param int                 $page
     * @param ParameterRepository $parameterRepository
     *
     * @return Response - frontend/article/index.html.twig
     */
    #[Route('{page<\d+>}', name: 'index', defaults: ['page' => 1])]
    public function indexFrontend(ArticleRepository $articleRepository, FeedService $feedService, PaginatorInterface $paginator, int $page, ParameterRepository $parameterRepository): Response
    {
        $articles = $articleRepository->findBy(['status' => 2, 'groupArticle' => null]);
        $feedService->addExternalFeeds($articles);
        if ($parameterRepository->findOneBy(['code' => Parameter::NEWS_PRIORITY])->getValue() == 2) {
            usort($articles, fn ($a, $b) => $b->getPublishedAt() <=> $a->getPublishedAt());
        }

        $articles = $paginator->paginate(
            $articles,
            $page,
            12
        );

        return $this->render('frontend/article/index.html.twig', [
            'articles' => $articles,
            'nbResultats' => count($articles),
        ]);
    }

    /**
     * Single article.
     *
     * @param Article           $article
     * @param ArticleRepository $articleRepository
     *
     * @return Response - frontend/article/one.html.twig
     */
    #[Route('{slug}', name: 'one')]
    public function one(Article $article, ArticleRepository $articleRepository): Response
    {
        $oneArticle = $articleRepository->findOneBy(['id' => $article, 'status' => 2, 'groupArticle' => null]);

        return $this->render('frontend/article/one.html.twig', [
            'article' => $oneArticle,
        ]);
    }
}
