<?php

namespace App\Controller\Backend;

use App\Entity\NewsletterCampaign;
use App\Form\NewsletterCampaignType;
use App\Repository\NewsletterCampaignRepository;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

#[Security('is_granted("ROLE_ADMIN")')]
#[Route('/administration/newsletter', name: 'admin_newsletter_')]
class NewsletterController extends AbstractController
{
    /**
     * @param NewsletterCampaignRepository $newsletterCampaignRepository
     *
     * @return \Symfony\Component\HttpFoundation\Response - backend/newsletter/index.html.twig
     */
    #[Route('', name: 'index')]
    public function index(NewsletterCampaignRepository $newsletterCampaignRepository)
    {
        $newsletterCampaigns = $newsletterCampaignRepository->findBy([], ['sendingTime' => 'DESC']);

        return $this->render('backend/newsletter/index.html.twig', [
            'newsletterCampaigns' => $newsletterCampaigns,
        ]);
    }

    #[Route('/nouvelle', name: 'new')]
    #[Route('/modifier/{id<\d+>}', name: 'edit')]
    public function handle(NewsletterCampaign $newsletterCampaign = null, Request $request, EntityManagerInterface $entityManager)
    {
        if (null === $newsletterCampaign) {
            $newsletterCampaign = new NewsletterCampaign();
        }

        $form = $this->createForm(NewsletterCampaignType::class, $newsletterCampaign);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $newsletterCampaign->setIsSent(false);
            $entityManager->persist($newsletterCampaign);
            $entityManager->flush();

            $this->addFlash(
                'success',
                'Campagne de newsletter enregistrée avec succès'
            );

            return $this->redirectToRoute('admin_newsletter_index');
        }

        return $this->render('backend/newsletter/new.html.twig', [
            'form' => $form->createView(),
            'newsletterCampaign' => $newsletterCampaign,
        ]);
    }

    #[Route('/supprimer/{id<\d+>}', name: 'delete')]
    public function delete(NewsletterCampaign $newsletterCampaign, EntityManagerInterface $entityManager)
    {
        $entityManager->remove($newsletterCampaign);
        $entityManager->flush();

        $this->addFlash(
            'success',
            'Campagne de newsletter supprimée avec succès'
        );

        return $this->redirectToRoute('admin_newsletter_index');
    }
}
