<?php

namespace App\Controller\Frontend;

use App\Entity\Resume;
use App\Form\ResumeType;
use App\Form\SearchResumeType;
use App\Repository\ResumeInstanceRepository;
use App\Repository\ResumeRepository;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/espace-carriere/cvtheque', name: 'resume')]
class ResumeController extends AbstractController
{
    /**
     * form to create a resume.
     *
     * @param Request                $request
     * @param EntityManagerInterface $entityManager
     * @param ResumeRepository       $resumeRepository
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response - frontend/edit.html.twig
     */
    #[Route('/mon-cv', name: '_my')]
    #[Security("is_granted('ROLE_STUDENT')")]
    public function create(Request $request, EntityManagerInterface $entityManager, ResumeRepository $resumeRepository)
    {
        $resume = $resumeRepository->findOneByUser($this->getUser()->getId());

        if (null === $resume) {
            $resume = (new Resume())
                ->setUser($this->getUser())
            ;
        }

        $form = $this->createForm(ResumeType::class, $resume);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $resume->setUser($this->getUser());
            $entityManager->persist($resume);
            $entityManager->flush();

            $this->addFlash(
                'success',
                'Votre CV a été déposé avec succès.'
            );

            return $this->redirectToRoute('resume_all');
        }

        return $this->render('frontend/resume/my.html.twig', [
            'form' => $form->createView(),
            'resume' => $resume,
        ]);
    }

    /**
     * form see all resumes.
     *
     * @param Request $request
     * @param ResumeRepository $resumeRepository
     * @param ResumeInstanceRepository $resumeInstanceRepository
     *
     * @return \Symfony\Component\HttpFoundation\Response - frontend/resume_all.html.twig
     */
    #[Route('/', name: '_all')]
    #[Security("is_granted('ROLE_STUDENT') or is_granted('ROLE_COMPANY') or is_granted('ROLE_ADMIN')")]
    public function index(Request $request, ResumeRepository $resumeRepository, ResumeInstanceRepository $resumeInstanceRepository)
    {
        $form = $this->createForm(SearchResumeType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();
            $resumes = $resumeRepository->findByFilters($data['keywords'], $data['activityArea'], $data['status'], $data['department'], $data['region']);
            $resumesOtherInstances = $resumeInstanceRepository->findByFilters($data['keywords'], $data['activityArea'], $data['status'], $data['department'], $data['region']);
        } else {
            $resumes = $resumeRepository->findAll();
            $resumesOtherInstances = $resumeInstanceRepository->findAll();
        }

        foreach ($resumesOtherInstances as $resumeOtherInstance) {
            $resumes[] = $resumeOtherInstance;
        }

        usort($resumes, function ($a, $b) {
            if (method_exists($a, 'getLastname')) {
                $a = $a->getLastname();
            } else {
                $a = $a->getUser()->getName();
            }
            if (method_exists($b, 'getLastname')) {
                $b = $b->getLastname();
            } else {
                $b = $b->getUser()->getName();
            }

            return strcmp($a, $b);
        });

        return $this->render('frontend/resume/index.html.twig', [
            'resumes' => $resumes,
            'form' => $form->createView(),
        ]);
    }
}
