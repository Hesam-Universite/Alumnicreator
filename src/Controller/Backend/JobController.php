<?php

namespace App\Controller\Backend;

use App\Entity\Parameter;
use App\Enum\StudyLevel;
use App\Enum\TypeOfContract;
use App\Repository\JobRepository;
use App\Repository\ParameterRepository;
use App\Repository\ResumeRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/administration/offres-emploi', name: 'admin_job_')]
class JobController extends AbstractController
{
    /**
     * Download a CSV file with all the jobs informations.
     *
     * @param JobRepository $jobRepository
     *
     * @return Response - The CSV file
     */
    #[Route('/export', name: 'export')]
    public function export(JobRepository $jobRepository): Response
    {
        $jobs = $jobRepository->findBy([], ['creationDate' => 'DESC']);

        $handle = fopen('php://temp', 'r+');

        fputcsv($handle, [
            'Date de création',
            'Titre',
            'Entreprise',
            'Description',
            'Secteur d\'activité',
            'Type de contrat',
            'Lieu',
            'Salaire',
            'Email de contact',
            'Lien vers l\'offre',
            'Niveau d\'étude',
        ]);

        foreach ($jobs as $job) {
            fputcsv($handle, [
                $job->getCreationDate()->format('d/m/Y'),
                $job->getTitle(),
                $job->getAuthor()->getCompanyName(),
                $job->getDescription(),
                $job->getActivityArea()->getName(),
                TypeOfContract::getLabel($job->getTypeOfContract()),
                $job->getCity(),
                $job->getRemuneration(),
                $job->getContactEmail(),
                $job->getLinkToTheJobOffer(),
                implode(', ', array_map(fn ($level) => StudyLevel::getLabel(StudyLevel::tryFrom($level)), $job->getDesiredLevel())),
            ]);
        }

        rewind($handle);

        $response = new Response(stream_get_contents($handle));
        fclose($handle);

        $response->headers->set('Content-Type', 'text/csv');
        $response->headers->set('Content-Disposition', 'attachment; filename="'.date('YmdHis_').'offres_emploi.csv"');

        return $response;
    }

    /**
     * To see all jobs from backend.
     *
     * @param JobRepository $jobRepository
     *
     * @return \Symfony\Component\HttpFoundation\Response - backend/index.html.twig
     */
    #[Route('/', name: 'index')]
    public function adminIndex(JobRepository $jobRepository)
    {
        $jobs = $jobRepository->findAll();

        return $this->render('backend/job/index.html.twig', [
            'jobs' => $jobs,
        ]);
    }

    /**
     * To approve or disapprove a job from backend.
     *
     * @param $id
     * @param JobRepository          $jobRepository
     * @param EntityManagerInterface $entityManager
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    #[Route('/approuver/{id<\d+>}', name: 'approve')]
    public function adminApproveStudent($id, JobRepository $jobRepository, EntityManagerInterface $entityManager, ResumeRepository $resumeRepository, MailerInterface $mailer, ParameterRepository $parameterRepository)
    {
        $job = $jobRepository->find($id);

        $job->setIsApproved(!$job->isApproved());

        if ($job->isApproved()) {
            $resumesWithSameActivityArea = $resumeRepository->findByActivityAreaAndAcceptedNotifications($job->getActivityArea());

            $emailFrom = $parameterRepository->findOneBy(['code' => Parameter::SMTP_FROM])->getValue();
            foreach ($resumesWithSameActivityArea as $resume) {
                $jobNotificationMail = (new Email())
                    ->from($emailFrom)
                    ->to($resume->getUser()->getEmail())
                    ->subject('Une nouvelle offre d\'emploi pourrait vous intéresser ! ')
                    ->html('<p>Une nouvelle offre est disponible et correspond à votre secteur d\'activité ! Connectez-nous dès maintenant sur Alumni Creator pour la découvrir.</p>');

                $mailer->send($jobNotificationMail);
            }
        }

        $entityManager->persist($job);
        $entityManager->flush();

        return $this->redirectToRoute('admin_job_index');
    }
}
