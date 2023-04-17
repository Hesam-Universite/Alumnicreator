<?php

namespace App\Controller\Frontend;

use App\Entity\Application;
use App\Entity\Job;
use App\Entity\Parameter;
use App\Form\ApplicationType;
use App\Repository\ApplicationRepository;
use App\Repository\JobRepository;
use App\Repository\ParameterRepository;
use App\Repository\ResumeRepository;
use App\Service\ApplicationService;
use App\Service\UtilService;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/espace-carriere/candidatures/', name: 'application_')]
class ApplicationController extends AbstractController
{
    /**
     * To create a new application.
     *
     * @param $id
     * @param Request                $request
     * @param JobRepository          $jobRepository
     * @param EntityManagerInterface $entityManager
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|Response - frontend/application/new.html.twig
     */
    #[Security("is_granted('ROLE_STUDENT') or is_granted('ROLE_ADMIN')")]
    #[Route('nouvelle/{id<\d+>}', name: 'new')]
    public function create($id, Request $request, JobRepository $jobRepository, EntityManagerInterface $entityManager, ParameterRepository $parameterRepository, MailerInterface $mailer)
    {
        $application = new Application();
        $form = $this->createForm(ApplicationType::class, $application);
        $form->handleRequest($request);

        $job = $jobRepository->findOneById($id);

        if ($form->isSubmitted() && $form->isValid()) {
            $application->setCreationDate(new \DateTime('now'));
            $application->setJob($job);
            $application->setUser($this->getUser());
            $entityManager->persist($application);
            $entityManager->flush();

            if ($job->getAuthor()->acceptedCandidacyNotification()) {
                $emailFrom = $parameterRepository->findOneBy(['code' => Parameter::SMTP_FROM])->getValue();
                $notification = (new Email())
                    ->from($emailFrom)
                    ->to($job->getAuthor()->getEmail())
                    ->subject('Nouveau candidat à l\'une de vos offres ! ')
                    ->html('<p>Un candidat a postulé à l\'une de vos offres sur la plateforme Alumni Creator. Connectez-vous pour la découvrir.</p>');

                $mailer->send($notification);
            }

            $this->addFlash(
                'success',
                'Votre candidature a été envoyée avec succès.',
            );

            return $this->redirectToRoute('job_index');
        }

        return $this->render('frontend/application/new.html.twig', [
            'form' => $form->createView(),
            'jobTitle' => $job->getTitle(),
            'jobId' => $job->getId(),
        ]);
    }

    /**
     * To see all applications of one job.
     *
     * @param Job                   $job
     * @param ApplicationRepository $applicationRepository
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|Response - frontend/application/index_by_job.html.twig
     */
    #[Security("is_granted('ROLE_ALUMNI') or is_granted('ROLE_COMPANY') or is_granted('ROLE_ADMIN')")]
    #[Route('{id<\d+>}', name: 'index_by_job')]
    public function indexByJob(Job $job, ApplicationRepository $applicationRepository)
    {
        if ($job->getAuthor() !== $this->getUser()) {
            $this->addFlash(
                'error',
                'Vous n\'avez pas accès aux candidatures de cette offre d\'emploi.',
            );

            return $this->redirectToRoute('resume_all');
        }

        $applications = $applicationRepository->findBy(['job' => $job], ['creationDate' => 'DESC']);

        return $this->render('frontend/application/index_by_job.html.twig', [
            'applications' => $applications,
            'jobTitle' => $job->getTitle(),
            'job' => $job,
        ]);
    }

    /**
     * Downloads an application in a zip format.
     *
     * @param Application        $application
     * @param ResumeRepository   $resumeRepository
     * @param UtilService        $utilService
     * @param ApplicationService $applicationService
     *
     * @return Response
     */
    #[Security("is_granted('ROLE_ALUMNI') or is_granted('ROLE_COMPANY') or is_granted('ROLE_ADMIN')")]
    #[Route('/espace-carriere/candidature/{id<\d+>}', name: 'one')]
    public function download(Application $application, ResumeRepository $resumeRepository, UtilService $utilService, ApplicationService $applicationService): Response
    {
        [$zipName, $zip] = $utilService->createZip();
        $resume = $resumeRepository->findOneByUser($application->getUser());
        $applicationService->addToZip($zip, $application, $resume);
        $zip->close();

        $response = new Response(file_get_contents($zipName));
        $response->headers->set('Content-Type', 'application/zip');
        $response->headers->set('Content-Disposition', 'attachment;filename="'.$application->getUser().'.zip"');
        $response->headers->set('Content-length', filesize($zipName));

        @unlink($zipName);

        return $response;
    }
}
