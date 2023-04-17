<?php

namespace App\Controller\Backend;

use App\Entity\SocialPost;
use App\Form\SocialPostType;
use App\Repository\ParameterRepository;
use App\Repository\SocialPostRepository;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

#[Security('is_granted("ROLE_ADMIN")')]
#[Route('/administration/posts-reseaux-sociaux/', name: 'admin_social_posts_')]
class SocialPostController extends AbstractController
{
    #[Route('index', name: 'index')]
    public function index(SocialPostRepository $socialPostRepository, ParameterRepository $parameterRepository)
    {
        $socialPosts = $socialPostRepository->findBy([], ['timeToPublished' => 'ASC']);

        $facebookPage = $parameterRepository->findOneBy(['code' => 'FAPI']);

        return $this->render('backend/social_post/index.html.twig', [
            'posts' => $socialPosts,
            'facebookPage' => $facebookPage,
        ]);
    }

    #[Route('nouveau', name: 'new')]
    #[Route('modifier/{id<\d+>}', name: 'edit')]
    public function handle(SocialPost $socialPost = null, Request $request, EntityManagerInterface $entityManager)
    {
        if (null === $socialPost) {
            $socialPost = new SocialPost();
        }

        $form = $this->createForm(SocialPostType::class, $socialPost);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $socialPost->setIsSent(false);
            $entityManager->persist($socialPost);
            $entityManager->flush();

            $this->addFlash(
                'success',
                'Post enregistré avec succès'
            );

            return $this->redirectToRoute('admin_social_posts_index');
        }

        return $this->render('backend/social_post/new.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('supprimer/{id<\d+>}', name: 'delete')]
    public function delete(SocialPost $socialPost, EntityManagerInterface $entityManager)
    {
        $entityManager->remove($socialPost);
        $entityManager->flush();

        $this->addFlash(
            'success',
            'Post supprimé avec succès'
        );

        return $this->redirectToRoute('admin_social_posts_index');
    }
}
