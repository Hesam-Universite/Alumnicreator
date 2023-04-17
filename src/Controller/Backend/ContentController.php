<?php

namespace App\Controller\Backend;

use App\Entity\Content;
use App\Form\ContentType;
use App\Repository\ContentRepository;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Security('is_granted("ROLE_ADMIN")')]
class ContentController extends AbstractController
{
    /**
     * Manage content of the frontend website.
     *
     * @param Request                $request
     * @param EntityManagerInterface $entityManager
     * @param ContentRepository      $contentRepository
     *
     * @return Response - backend/parameter/content.html.twig
     */
    #[Route('/administration/gestion-des-contenus', name: 'admin_parameters_content')]
    public function content(Request $request, EntityManagerInterface $entityManager, ContentRepository $contentRepository): Response
    {
        $content = $contentRepository->findOneBy([], ['id' => 'ASC']);

        if (null === $content) {
            $content = new Content();
        }

        $form = $this->createForm(ContentType::class, $content);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($content);
            $entityManager->flush();

            $this->addFlash(
                'success',
                'Contenu enregistré avec succès'
            );
        }

        return $this->render('backend/parameter/content.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
