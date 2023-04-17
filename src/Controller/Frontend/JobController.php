<?php

namespace App\Controller\Frontend;

use App\Entity\Job;
use App\Entity\Parameter;
use App\Form\JobType;
use App\Form\SearchJobType;
use App\Repository\ApplicationRepository;
use App\Repository\InstanceRepository;
use App\Repository\JobInstanceRepository;
use App\Repository\JobRepository;
use App\Repository\ParameterRepository;
use App\Repository\ResumeRepository;
use App\Repository\UserInstanceRepository;
use App\Repository\UserRepository;
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

#[Route('/espace-carriere/offres-emploi', name: 'job_')]
class JobController extends AbstractController
{
    /**
     * To create a new job.
     *
     * @param Request                $request
     * @param EntityManagerInterface $entityManager
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|Response - frontend/new.html.twig
     */
    #[Security("is_granted('ROLE_ALUMNI') or is_granted('ROLE_COMPANY') or is_granted('ROLE_ADMIN')")]
    #[Route('/nouvelle', name: 'new')]
    public function new(Request $request, EntityManagerInterface $entityManager, ResumeRepository $resumeRepository, MailerInterface $mailer, ParameterRepository $parameterRepository)
    {
        $job = new Job();
        $form = $this->createForm(JobType::class, $job);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $job->setAuthor($this->getUser());
            if (in_array('ROLE_COMPANY', $job->getAuthor()->getRoles())) {
                $job->setIsApproved(false);
            } else {
                $job->setIsApproved(true);

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
            $job->setCreationDate(new \DateTime('now'));
            $entityManager->persist($job);
            $entityManager->flush();

            if ($job->isApproved()) {
                $this->addFlash(
                    'success',
                    'Votre offre d\'emploi est publiée.'
                );
            } else {
                $this->addFlash(
                    'success',
                    'Merci, nous avons reçu votre offre d\'emploi. Cette dernière sera vérifiée par notre équipe avant d\'être publiée. '
                );
            }

            return $this->redirectToRoute('resume_all');
        }

        return $this->render('frontend/job/new.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * To see all jobs.
     *
     * @param JobRepository $jobRepository
     *
     * @return Response
     */
    #[Security("is_granted('ROLE_STUDENT') or is_granted('ROLE_ADMIN')")]
    #[Route('/', name: 'index')]
    public function index(JobRepository $jobRepository, Request $request, JobInstanceRepository $jobInstanceRepository)
    {
        $form = $this->createForm(SearchJobType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();
            $jobs = $jobRepository->findByFilters($data['activityArea'], $data['department'], $data['region']);
            $jobsOtherInstances = $jobInstanceRepository->findByFilters($data['activityArea'], $data['department'], $data['region']);
        } else {
            $jobs = $jobRepository->findAllApproved();
            $jobsOtherInstances = $jobInstanceRepository->findAll();
        }

        foreach ($jobsOtherInstances as $jobOtherInstance) {
            $jobs[] = $jobOtherInstance;
        }

        usort($jobs, function ($a, $b) {
            return $a->getCreationDate() <=> $b->getCreationDate();
        });

        return $this->render('frontend/job/index.html.twig', [
            'jobs' => $jobs,
            'form' => $form->createView(),
        ]);
    }

    /**
     * To see all jobs of one company.
     *
     * @param JobRepository $jobRepository
     *
     * @return Response
     */
    #[Security("is_granted('ROLE_STUDENT') or is_granted('ROLE_ADMIN')")]
    #[Route('/entreprise/{id<\d+>}', name: 'index_by_company')]
    public function indexByCompany($id, JobRepository $jobRepository, UserRepository $userRepository)
    {
        $jobs = $jobRepository->findByAuthor($id);
        $companyName = $userRepository->find($id)->getCompanyName();
        $companyId = $userRepository->find($id)->getId();

        return $this->render('frontend/job/index_by_company.html.twig', [
            'jobs' => $jobs,
            'companyName' => $companyName,
            'companyId' => $companyId,
        ]);
    }

    /**
     * To see a job.
     *
     * @param $id
     * @param JobRepository $jobRepository
     *
     * @return Response - frontend/one.html.twig
     */
    #[Security("is_granted('ROLE_STUDENT') or is_granted('ROLE_COMPANY') or is_granted('ROLE_ADMIN')")]
    #[Route('/{id<\d+>}', name: 'one')]
    public function view($id, JobRepository $jobRepository): Response
    {
        $job = $jobRepository->findOneById($id);

        return $this->render('frontend/job/one.html.twig', [
            'job' => $job,
        ]);
    }

    /**
     * To see a job get from another instance.
     *
     * @param $id
     * @param JobInstanceRepository $jobInstanceRepository
     * @param InstanceRepository $instanceRepository
     * @param UserInstanceRepository $userInstanceRepository
     *
     * @return Response - frontend/one_instance.html.twig
     */
    #[Security("is_granted('ROLE_STUDENT') or is_granted('ROLE_COMPANY') or is_granted('ROLE_ADMIN')")]
    #[Route('/i/{id<\d+>}', name: 'one_instance')]
    public function viewInstance($id, JobInstanceRepository $jobInstanceRepository, InstanceRepository $instanceRepository, UserInstanceRepository $userInstanceRepository): Response
    {
        $job = $jobInstanceRepository->findOneBy(['id' => $id]);
        $instance = $instanceRepository->findOneBy(['externalId' => $job->getInstanceId()]);
        $user = $userInstanceRepository->findOneBy(['otherInstanceId' => $job->getAuthorId(), 'instanceId' => $job->getInstanceId()]);

        return $this->render('frontend/job/one_instance.html.twig', [
            'job' => $job,
            'instance' => $instance,
            'user' => $user,
        ]);
    }

    /**
     * To see the connected user's jobs.
     *
     * @param JobRepository $jobRepository
     *
     * @return Response
     */
    #[Security("is_granted('ROLE_ALUMNI') or is_granted('ROLE_COMPANY') or is_granted('ROLE_ADMIN')")]
    #[Route('/mes-offres', name: 'my')]
    public function indexByUser(JobRepository $jobRepository)
    {
        $jobs = $jobRepository->findByAuthor($this->getUser());

        return $this->render('frontend/job/my_jobs.html.twig', [
            'jobs' => $jobs,
        ]);
    }

    /**
     * To edit a job.
     *
     * @param $id
     * @param JobRepository          $jobRepository
     * @param Request                $request
     * @param EntityManagerInterface $entityManager
     *
     * @return string|\Symfony\Component\HttpFoundation\RedirectResponse|Response - frontend/edit.html.twig
     */
    #[Security("is_granted('ROLE_ALUMNI') or is_granted('ROLE_COMPANY') or is_granted('ROLE_ADMIN')")]
    #[Route('/{id<\d+>}/modifier', name: 'edit')]
    public function edit($id, JobRepository $jobRepository, Request $request, EntityManagerInterface $entityManager)
    {
        $job = $jobRepository->findOneById($id);

        if ($job->getAuthor() !== $this->getUser()) {
            throw new \Exception('Vous n\'êtes pas autorisé à modifier cette offre.');
        }

        $form = $this->createForm(JobType::class, $job);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($job);
            $entityManager->flush();

            $this->addFlash(
                'success',
                'Votre offre d\'emploi est à jour.'
            );

            return $this->redirectToRoute('job_my');
        }

        return $this->render('frontend/job/edit.html.twig', [
            'form' => $form->createView(),
            'job' => $job,
        ]);
    }

    /**
     * To delete a job.
     *
     * @param $id
     * @param JobRepository          $jobRepository
     * @param EntityManagerInterface $entityManager
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    #[Security("is_granted('ROLE_ALUMNI') or is_granted('ROLE_COMPANY') or is_granted('ROLE_ADMIN')")]
    #[Route('/{id<\d+>}/supprimer', name: 'delete')]
    public function delete($id, JobRepository $jobRepository, EntityManagerInterface $entityManager)
    {
        $jobToDelete = $jobRepository->findOneById($id);

        if ($jobToDelete->getAuthor() !== $this->getUser()) {
            throw new \Exception('Vous n\'êtes pas autorisé à supprimer cette offre.');
        }

        $entityManager->remove($jobToDelete);
        $entityManager->flush();

        $this->addFlash(
            'success',
            'Offre d\'emploi supprimée avec succès',
        );

        return $this->redirectToRoute('job_my');
    }

    #[Route('/{id<\d+>}/telecharger-toutes-candidatures', name: 'download_all_applications')]
    public function downloadAll(Job $job, ApplicationRepository $applicationRepository, ResumeRepository $resumeRepository, UtilService $utilService, ApplicationService $applicationService): Response
    {
        $applications = $applicationRepository->findByJob($job);

        [$zipName, $zip] = $utilService->createZip();

        foreach ($applications as $application) {
            $resume = $resumeRepository->findOneByUser($application->getUser());
            $applicationService->addToZip($zip, $application, $resume);
        }

        $zip->close();

        $response = new Response(file_get_contents($zipName));
        $response->headers->set('Content-Type', 'application/zip');
        $response->headers->set('Content-Disposition', 'attachment;filename="candidatures.zip"');
        $response->headers->set('Content-length', filesize($zipName));

        @unlink($zipName);

        return $response;
    }
}
