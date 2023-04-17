<?php

namespace App\Controller\Frontend;

use App\Entity\Page;
use App\Repository\PageRepository;
use App\Repository\ParameterRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/page', name: 'page_')]
class PageController extends AbstractController
{
    /**
     * Single page.
     *
     * @param Page           $page
     * @param PageRepository $pageRepository
     *
     * @return Response - frontend/page/one.html.twig
     */
    #[Route('/{slug}', name: 'one')]
    public function one(Page $page, PageRepository $pageRepository): Response
    {
        $onePage = $pageRepository->findOneBy(['id' => $page]);

        return $this->render('frontend/page/one.html.twig', [
            'page' => $onePage,
        ]);
    }

    #[Route('/mentions-legales/politique-de-confidentialite', name: 'privacy_policy')]
    public function privacyPolicy(ParameterRepository $parameterRepository): Response
    {
        $page = (new Page())
            ->setTitle('Politique de confidentialité')
            ->setContent($parameterRepository->findOneBy(['code' => 'COPP'])->getValue())
        ;

        return $this->render('frontend/page/one.html.twig', [
            'page' => $page,
        ]);
    }

    #[Route('/mentions-legales/vie-privee-et-cookies', name: 'privacy_and_cookies')]
    public function privacyAndCookies(ParameterRepository $parameterRepository): Response
    {
        $page = (new Page())
            ->setTitle('Vie privée et Cookies')
            ->setContent($parameterRepository->findOneBy(['code' => 'COPC'])->getValue())
        ;

        return $this->render('frontend/page/one.html.twig', [
            'page' => $page,
        ]);
    }
}
