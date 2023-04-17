<?php

namespace App\Controller\Frontend;

use App\Repository\ArticleRepository;
use App\Repository\ContentRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
{
    #[Route('/', name: 'home')]
    public function home(ArticleRepository $articleRepository, ContentRepository $contentRepository)
    {
        $articles = $articleRepository->findBy(['groupArticle' => null], ['publishedAt' => 'DESC'], 3);

        return $this->render('frontend/home.html.twig', [
            'articles' => $articles,
        ]);
    }
}
