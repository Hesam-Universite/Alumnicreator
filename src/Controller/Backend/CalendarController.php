<?php

namespace App\Controller\Backend;

use App\Entity\Event;
use App\Form\EventType;
use App\Repository\EventRepository;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Security('is_granted("ROLE_ADMIN")')]
#[Route('/administration/agenda', name: 'calendar_admin_')]
class CalendarController extends AbstractController
{
    /**
     * display all events in the backoffice.
     *
     * @param EventRepository $eventRepository
     *
     * @return Response - backend/calendar/index.html.twig
     */
    #[Route('/', name: 'index')]
    public function indexAdmin(EventRepository $eventRepository): Response
    {
        $eventsToCome = $eventRepository->eventsToCome();
        $pastEvents = $eventRepository->pastEvents();

        return $this->render('backend/calendar/index.html.twig', [
            'eventsToCome' => $eventsToCome,
            'pastEvents' => $pastEvents,
        ]);
    }

    /**
     * handle an event.
     *
     * @param Event|null             $event
     * @param Request                $request
     * @param EntityManagerInterface $entityManager
     *
     * @return Response - backend/calendar/new.html.twig
     */
    #[Route('/nouvel-evenement', name: 'new')]
    #[Route('/evenement/modifier/{id<\d+>}', name: 'edit')]
    public function handle(Event $event = null, Request $request, EntityManagerInterface $entityManager): Response
    {
        if (null === $event) {
            $event = new Event();
        }

        $form = $this->createForm(EventType::class, $event);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            if ($form->getData()->isAllDay()) {
                $event->setStart($form->getData()->getStartFullday());
                $event->setEnd($form->getData()->getEndFullday());
            }
            if ($form->getData()->getStart() < $form->getData()->getEnd()) {
                $entityManager->persist($event);
                $entityManager->flush();

                $this->addFlash(
                    'success',
                    'Événement enregistré'
                );
            } else {
                $this->addFlash(
                    'error',
                    'Erreur : La date de fin est antérieure à la date de départ'
                );
            }

            return $this->redirectToRoute('calendar_admin_index');
        }

        return $this->render('backend/calendar/new.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * delete an event.
     *
     * @param Event                  $event
     * @param EntityManagerInterface $entityManager
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse - redirect to route calendar_admin_index
     */
    #[Route('/evenement/supprimer/{id<\d+>}', name: 'delete')]
    public function delete(Event $event, EntityManagerInterface $entityManager)
    {
        $entityManager->remove($event);
        $entityManager->flush();

        $this->addFlash(
            'success',
            'Événements supprimé avec succès',
        );

        return $this->redirectToRoute('calendar_admin_index');
    }
}
