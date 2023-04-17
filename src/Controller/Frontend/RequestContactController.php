<?php

namespace App\Controller\Frontend;

use App\Entity\RequestContact;
use App\Form\RequestContactType;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/contact', name: 'contact')]
#[Security("is_granted('ROLE_STUDENT') or is_granted('ROLE_COMPANY') or is_granted('ROLE_ADMIN')")]
class RequestContactController extends AbstractController
{
    #[Route('/{object<\d+>}', name: '')]
    public function contact($object, Request $request, EntityManagerInterface $entityManager)
    {
        $requestContact = (new RequestContact())
            ->setEmail($this->getUser()->getEmail());

        switch ($object) {
            case 1:
                $requestContact->setObject('');
                break;
            case 2:
                $requestContact->setObject("Changer de nom / prénom dans l'annuaire");
                break;
            case 3:
                $requestContact->setObject('Demande de suppression de compte');
                break;
        }

        $form = $this->createForm(RequestContactType::class, $requestContact);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $requestContact->setUser($this->getUser());
            $requestContact->setCreationDate(new \DateTime());

            $entityManager->persist($requestContact);
            $entityManager->flush();

            $this->addFlash(
                'success',
                'Votre demande de contact sera traitée dans les plus brefs délais.',
            );

            return $this->redirectToRoute('resume_all');
        }

        return $this->render('frontend/contact/new.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
