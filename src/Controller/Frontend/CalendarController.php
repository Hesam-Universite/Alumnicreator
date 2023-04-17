<?php

namespace App\Controller\Frontend;

use App\Repository\EventRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Security("is_granted('ROLE_STUDENT') or is_granted('ROLE_ADMIN')")]
#[Route('/agenda', name: 'calendar_')]
class CalendarController extends AbstractController
{
    /**
     * display all events.
     *
     * @param EventRepository $eventRepository
     *
     * @return Response - frontend/calendar/index.html.twig
     */
    #[Route('/', name: 'index')]
    public function index(EventRepository $eventRepository): Response
    {
        $events = $eventRepository->findBy(['groupEvent' => null]);

        return $this->render('frontend/calendar/index.html.twig', [
            'events' => $events,
        ]);
    }

    /**
     * Create an ics file.
     *
     * @param EventRepository $eventRepository
     *
     * @return Response - return an ics file
     */
    #[Route('/lien-ics', name: 'download')]
    public function icsLink(EventRepository $eventRepository)
    {
        $events = $eventRepository->findBy(['groupEvent' => null]);

        $handle = fopen('php://temp', 'r+');

        $ics = "BEGIN:VCALENDAR\n";
        $ics .= "VERSION:2.0\n";
        $ics .= "PRODID:-//hacksw/handcal//NONSGML v1.0//EN\n";

        foreach ($events as $event) {
            $date_debut = strtotime($event->getStart()->format('Y-m-d H:i:s'));
            $date_fin = strtotime($event->getEnd()->format('Y-m-d H:i:s'));
            $objet = $event->getTitle();

            // Event in ICS format
            $ics .= "BEGIN:VEVENT\n";
            $ics .= "X-WR-TIMEZONE:Europe/Paris\n";
            $ics .= 'DTSTART:'.date('Ymd', $date_debut).'T'.date('His', $date_debut)."\n";
            $ics .= 'DTEND:'.date('Ymd', $date_fin).'T'.date('His', $date_fin)."\n";
            $ics .= 'SUMMARY:'.$objet."\n";
            $ics .= "END:VEVENT\n";
        }

        $ics .= "END:VCALENDAR\n";

        fwrite($handle, $ics);

        rewind($handle);

        $response = new Response(stream_get_contents($handle));
        $response->headers->set('Content-type', 'text/calendar');
        $response->headers->set('Content-Disposition', 'attachment; filename="'.date('YmdHis_').'calendrier.ics"');

        fclose($handle);

        return $response;
    }
}
