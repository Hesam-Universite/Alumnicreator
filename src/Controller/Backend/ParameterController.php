<?php

namespace App\Controller\Backend;

use App\Entity\ActivityArea;
use App\Entity\CustomFont;
use App\Entity\Instance;
use App\Entity\Skill;
use App\Form\ActivityAreaType;
use App\Form\CustomFontType;
use App\Form\InstanceType;
use App\Form\SkillType;
use App\Repository\ActivityAreaRepository;
use App\Repository\CustomFontRepository;
use App\Repository\DirectoryPageInstanceRepository;
use App\Repository\GroupRepository;
use App\Repository\InstanceRepository;
use App\Repository\JobInstanceRepository;
use App\Repository\JobRepository;
use App\Repository\ParameterRepository;
use App\Repository\ResumeInstanceRepository;
use App\Repository\ResumeRepository;
use App\Repository\SkillRepository;
use App\Repository\UserInstanceRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\DecodingExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

#[Security('is_granted("ROLE_ADMIN")')]
#[Route('/administration/parametres', name: 'admin_parameters_')]
class ParameterController extends AbstractController
{
    /**
     * Edit instance parameters.
     *
     * @param Request                $request
     * @param ParameterRepository    $parameterRepository
     * @param EntityManagerInterface $entityManager
     *
     * @return Response - backend/parameter/set.html.twig
     */
    #[IsGranted('ROLE_SUPER_ADMIN')]
    #[Route('/', name: 'set')]
    public function set(Request $request, ParameterRepository $parameterRepository, EntityManagerInterface $entityManager): Response
    {
        if ($request->isMethod('POST')) {
            foreach ($request->request->all() as $code => $value) {
                $parameter = $parameterRepository->findOneBy(['code' => $code]);

                if ($parameter) {
                    if ('ADEM' === $parameter->getCode()) {
                        if (!filter_var($value, FILTER_VALIDATE_EMAIL)) {
                            $this->addFlash(
                                type: 'error',
                                message: 'Le mail est invalide et n\'a donc pas été mis à jour.'
                            );
                            continue;
                        }
                    }
                    $parameter->setValue($value);
                    $entityManager->persist($parameter);
                }
            }

            $entityManager->flush();

            $this->addFlash(
                type: 'success',
                message: 'Les paramètres ont bien été mis à jour.'
            );

            return $this->redirectToRoute('admin_parameters_set');
        }

        $parameters = $parameterRepository->findAll();
        $parametersSorted = [];

        foreach ($parameters as $parameter) {
            $parametersSorted[$parameter->getCode()] = $parameter->getValue();
        }

        return $this->render('backend/parameter/set.html.twig', [
            'parameters' => $parametersSorted,
        ]);
    }

    /**
     * To config design on the frontend website.
     *
     * @param Request                $request
     * @param ParameterRepository    $parameterRepository
     * @param EntityManagerInterface $entityManager
     * @param CustomFontRepository   $customFontRepository
     *
     * @return Response - backend/parameter/visual.html.twig
     */
    #[IsGranted('ROLE_SUPER_ADMIN')]
    #[Route('/configurations-visuelles', name: 'visual')]
    public function visual(Request $request, ParameterRepository $parameterRepository, EntityManagerInterface $entityManager, CustomFontRepository $customFontRepository): Response
    {
        if ($request->isMethod('POST')) {
            foreach ($request->request->all() as $code => $value) {
                $parameter = $parameterRepository->findOneBy(['code' => $code]);

                if ($parameter) {
                    $parameter->setValue($value);
                    $entityManager->persist($parameter);
                }
            }

            $entityManager->flush();

            $this->addFlash(
                type: 'success',
                message: 'Les paramètres ont bien été mis à jour.'
            );

            return $this->redirectToRoute('admin_parameters_visual');
        }

        $parameters = $parameterRepository->findAll();
        $parametersSorted = [];

        foreach ($parameters as $parameter) {
            $parametersSorted[$parameter->getCode()] = $parameter->getValue();
        }

        $customFonts = $customFontRepository->findAll();

        return $this->render('backend/parameter/visual.html.twig', [
            'parameters' => $parametersSorted,
            'everyCustomFonts' => $customFonts,
        ]);
    }

    /**
     * index custom fonts.
     *
     * @param CustomFontRepository $customFontRepository
     *
     * @return Response - backend/parameter/index_fonts.html.twig
     */
    #[IsGranted('ROLE_SUPER_ADMIN')]
    #[Route('/polices-personnalisees', name: 'index_fonts')]
    public function indexFonts(CustomFontRepository $customFontRepository): Response
    {
        $fonts = $customFontRepository->findAll();

        return $this->render('backend/parameter/index_fonts.html.twig', [
            'fonts' => $fonts,
        ]);
    }

    /**
     * add a new custom font.
     *
     * @param Request                $request
     * @param EntityManagerInterface $entityManager
     *
     * @return Response - backend/parameter/new_font.html.twig
     */
    #[IsGranted('ROLE_SUPER_ADMIN')]
    #[Route('/nouvelle-police', name: 'new_font')]
    public function newCustomFont(Request $request, EntityManagerInterface $entityManager): Response
    {
        $customFont = new CustomFont();

        $form = $this->createForm(CustomFontType::class, $customFont);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($customFont);
            $entityManager->flush();

            $this->addFlash(
                'success',
                'Police ajoutée avec succès.'
            );

            return $this->redirectToRoute('admin_parameters_index_fonts');
        }

        return $this->render('backend/parameter/new_font.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * index every instances.
     *
     * @param InstanceRepository $instanceRepository
     *
     * @return Response - backend/parameter/index_instances.html.twig
     */
    #[IsGranted('ROLE_SUPER_ADMIN')]
    #[Route('/instances', name: 'index_instances')]
    public function indexInstances(InstanceRepository $instanceRepository): Response
    {
        $instances = $instanceRepository->findAll();

        return $this->render('backend/parameter/index_instances.html.twig', [
            'instances' => $instances,
        ]);
    }

    /**
     * handle instance.
     *
     * @param Instance|null          $instance
     * @param Request                $request
     * @param EntityManagerInterface $entityManager
     * @param HttpClientInterface    $client
     *
     * @return Response - backend/parameter/new_instance.html.twig
     *
     * @throws ClientExceptionInterface
     * @throws DecodingExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws ServerExceptionInterface
     * @throws TransportExceptionInterface
     */
    #[IsGranted('ROLE_SUPER_ADMIN')]
    #[Route('/instances/nouvelle', name: 'new_instance')]
    #[Route('/instances/modifier/{id<\d+>}', name: 'edit_instance')]
    public function handleInstance(Instance $instance = null, Request $request, EntityManagerInterface $entityManager, HttpClientInterface $client): Response
    {
        if (null === $instance) {
            $instance = new Instance();
        }

        $form = $this->createForm(InstanceType::class, $instance);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $instance->setAllowOtherInstance(false);
            if (str_ends_with($form->getData()->getInstanceUrl(), '/')) {
                $instance->setInstanceUrl(substr($form->getData()->getInstanceUrl(), 0, -1));
            }

            // We call the API if the ID of the other instance is not null or empty
            if (null != $instance->getExternalId()) {
                $response = $client->request('POST', $instance->getInstanceUrl().'/api/connect-instance', [
                    'json' => [
                        'localId' => $instance->getLocalId(),
                        'externalId' => $instance->getExternalId(),
                    ],
                ]);
                if (!empty($response->toArray()['localId']) && $response->toArray()['localId'] == $instance->getExternalId()) {
                    $instance->setAllowOtherInstance(true);
                }
            }

            $entityManager->persist($instance);
            $entityManager->flush();

            $this->addFlash(
                'success',
                'Instance ajoutée avec succès'
            );

            return $this->redirectToRoute('admin_parameters_index_instances');
        }

        return $this->render('backend/parameter/new_instance.html.twig', [
            'form' => $form->createView(),
            'instance' => $instance,
        ]);
    }

    /**
     * delete an instance.
     *
     * @param Instance|null                   $instance
     * @param EntityManagerInterface          $entityManager
     * @param HttpClientInterface             $client
     * @param DirectoryPageInstanceRepository $directoryPageInstanceRepository
     * @param JobInstanceRepository           $jobInstanceRepository
     * @param ResumeInstanceRepository        $resumeInstanceRepository
     * @param UserInstanceRepository          $userInstanceRepository
     *
     * @return RedirectResponse - admin_parameters_index_instances
     *
     * @throws TransportExceptionInterface
     */
    #[IsGranted('ROLE_SUPER_ADMIN')]
    #[Route('/instances/supprimer/{id<\d+>}', name: 'delete_instance')]
    public function deleteInstance(Instance $instance = null, EntityManagerInterface $entityManager, HttpClientInterface $client, DirectoryPageInstanceRepository $directoryPageInstanceRepository, JobInstanceRepository $jobInstanceRepository, ResumeInstanceRepository $resumeInstanceRepository, UserInstanceRepository $userInstanceRepository): RedirectResponse
    {
        // We call the API if the ID of the other instance is not null or empty
        $entityManager->remove($instance);

        $directoriesPages = $directoryPageInstanceRepository->findBy(['instanceId' => $instance->getExternalId()]);
        foreach ($directoriesPages as $directoriesPage) {
            $entityManager->remove($directoriesPage);
        }

        $jobs = $jobInstanceRepository->findBy(['instanceId' => $instance->getExternalId()]);
        foreach ($jobs as $job) {
            $entityManager->remove($job);
        }

        $resumes = $resumeInstanceRepository->findBy(['instanceId' => $instance->getExternalId()]);
        foreach ($resumes as $resume) {
            $entityManager->remove($resume);
        }

        $users = $userInstanceRepository->findBy(['instanceId' => $instance->getExternalId()]);
        foreach ($users as $user) {
            $entityManager->remove($user);
        }

        $entityManager->flush();

        if ($instance->isAllowOtherInstance()) {
            $client->request('POST', $instance->getInstanceUrl().'/api/disconnect-instance', [
                'json' => [
                    'localId' => $instance->getLocalId(),
                    'externalId' => $instance->getExternalId(),
                ],
            ]);
        }

        $this->addFlash(
            'success',
            'Instance supprimée avec succès',
        );

        return $this->redirectToRoute('admin_parameters_index_instances');
    }

    /**
     * delete a custom font.
     *
     * @param CustomFont             $customFont
     * @param EntityManagerInterface $entityManager
     *
     * @return RedirectResponse - admin_parameters_index_fonts
     */
    #[IsGranted('ROLE_SUPER_ADMIN')]
    #[Route('/supprimer-police/{id<\d+>}', name: 'delete_font')]
    public function deleteFont(CustomFont $customFont, EntityManagerInterface $entityManager): RedirectResponse
    {
        $entityManager->remove($customFont);
        $entityManager->flush();

        $this->addFlash(
            'success',
            'Police supprimée avec succès',
        );

        return $this->redirectToRoute('admin_parameters_index_fonts');
    }

    /**
     * edit legal notice.
     *
     * @param ParameterRepository    $parameterRepository
     * @param Request                $request
     * @param EntityManagerInterface $entityManager
     *
     * @return Response - backend/parameter/legal_notice.html.twig
     */
    #[Route('/mentions-legales', name: 'legal-notice')]
    public function legalNotice(ParameterRepository $parameterRepository, Request $request, EntityManagerInterface $entityManager): Response
    {
        if ($request->isMethod('POST')) {
            foreach ($request->request->all() as $code => $value) {
                $parameter = $parameterRepository->findOneBy(['code' => $code]);

                if ($parameter) {
                    $parameter->setValue($value);
                    $entityManager->persist($parameter);
                }
            }

            $entityManager->flush();

            $this->addFlash(
                type: 'success',
                message: 'Modifications enregistrées avec succès.'
            );

            return $this->redirectToRoute('admin_parameters_legal-notice');
        }

        $parameters = $parameterRepository->findAll();
        $parametersSorted = [];

        foreach ($parameters as $parameter) {
            $parametersSorted[$parameter->getCode()] = $parameter->getValue();
        }

        return $this->render('backend/parameter/legal_notice.html.twig', [
            'parameters' => $parametersSorted,
        ]);
    }

    /**
     * Index activity area.
     *
     * @param ActivityAreaRepository $activityAreaRepository
     *
     * @return Response - backend/parameter/index-activityarea.html.twig
     */
    #[Route('/secteurs-dactivite', name: 'index-activityarea')]
    public function indexActivityArea(ActivityAreaRepository $activityAreaRepository): Response
    {
        $activityArea = $activityAreaRepository->findAll();

        return $this->render('backend/parameter/index-activityarea.html.twig', [
            'activityArea' => $activityArea,
        ]);
    }

    /**
     * handle activity area.
     *
     * @param ActivityArea|null      $activityArea
     * @param Request                $request
     * @param EntityManagerInterface $entityManager
     *
     * @return Response - backend/parameter/handle-activityarea.html.twig
     */
    #[Route('/secteurs-dactivite/nouveau', name: 'new-activityarea')]
    #[Route('/secteurs-dactivite/{id<\d+>}/modifier', name: 'edit-activityarea')]
    public function handleActivityArea(ActivityArea $activityArea = null, Request $request, EntityManagerInterface $entityManager): Response
    {
        if (null === $activityArea) {
            $activityArea = new ActivityArea();
        }

        $form = $this->createForm(ActivityAreaType::class, $activityArea);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($activityArea);
            $entityManager->flush();

            $this->addFlash(
                'success',
                'Secteur d\'activité enregistré avec succès'
            );

            return $this->redirectToRoute('admin_parameters_index-activityarea');
        }

        return $this->render('backend/parameter/handle-activityarea.html.twig', [
            'form' => $form->createView(),
            'activityArea' => $activityArea,
        ]);
    }

    /**
     * delete activity area.
     *
     * @param ActivityArea           $activityArea
     * @param EntityManagerInterface $entityManager
     * @param GroupRepository        $groupRepository
     * @param JobRepository          $jobRepository
     * @param ResumeRepository       $resumeRepository
     * @param UserRepository         $userRepository
     *
     * @return RedirectResponse - admin_parameters_index-activityarea
     */
    #[Route('/secteurs-dactivite/{id<\d+>}/supprimer', name: 'delete-activityarea')]
    public function deleteActivityArea(ActivityArea $activityArea, EntityManagerInterface $entityManager, GroupRepository $groupRepository, JobRepository $jobRepository, ResumeRepository $resumeRepository, UserRepository $userRepository): RedirectResponse
    {
        $groupsWithTheActivityAreaToDelete = $groupRepository->findBy(['activityArea' => $activityArea]);
        $jobsWithTheActivityAreaToDelete = $jobRepository->findBy(['activityArea' => $activityArea]);
        $resumesWithTheActivityAreaToDelete = $resumeRepository->findBy(['activityArea' => $activityArea]);
        $usersWithTheActivityAreaToDelete = $userRepository->findBy(['activityArea' => $activityArea]);

        if (null != $groupsWithTheActivityAreaToDelete || null != $jobsWithTheActivityAreaToDelete || null != $resumesWithTheActivityAreaToDelete || null != $usersWithTheActivityAreaToDelete) {
            $this->addFlash(
                'error',
                'Ce secteur d\'activité est attribué à des éléments sur la plateforme.',
            );
        } else {
            $entityManager->remove($activityArea);
            $entityManager->flush();

            $this->addFlash(
                'success',
                'Secteur d\'activité supprimé avec succès',
            );
        }

        return $this->redirectToRoute('admin_parameters_index-activityarea');
    }

    /**
     * Index skills.
     *
     * @param SkillRepository $skillRepository
     *
     * @return Response - backend/parameter/index-skills.html.twig
     */
    #[Route('/competences', name: 'index-skills')]
    public function indexSkills(SkillRepository $skillRepository): Response
    {
        $skills = $skillRepository->findAll();

        return $this->render('backend/parameter/index-skills.html.twig', [
            'skills' => $skills,
        ]);
    }

    /**
     * handle skill.
     *
     * @param Skill|null             $skill
     * @param Request                $request
     * @param EntityManagerInterface $entityManager
     *
     * @return Response - backend/parameter/handle-skill.html.twig
     */
    #[Route('/competences/nouveau', name: 'new-skill')]
    #[Route('/competences/{id<\d+>}/modifier', name: 'edit-skill')]
    public function handleSkill(Skill $skill = null, Request $request, EntityManagerInterface $entityManager): Response
    {
        if (null === $skill) {
            $skill = new Skill();
        }

        $form = $this->createForm(SkillType::class, $skill);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($skill);
            $entityManager->flush();

            $this->addFlash(
                'success',
                'Compétence enregistrée avec succès'
            );

            return $this->redirectToRoute('admin_parameters_index-skills');
        }

        return $this->render('backend/parameter/handle-activityarea.html.twig', [
            'form' => $form->createView(),
            'skill' => $skill,
        ]);
    }

    /**
     * delete skill.
     *
     * @param Skill                  $skill
     * @param EntityManagerInterface $entityManager
     * @param ResumeRepository       $resumeRepository
     *
     * @return RedirectResponse - admin_parameters_index-skills
     */
    #[Route('/competence/{id<\d+>}/supprimer', name: 'delete-skill')]
    public function deleteSkill(Skill $skill, EntityManagerInterface $entityManager, ResumeRepository $resumeRepository): RedirectResponse
    {
        $resumesWithTheSkillToDelete = $resumeRepository->findBy(['skill' => $skill]);

        if (null != $resumesWithTheSkillToDelete) {
            $this->addFlash(
                'error',
                'Cette compétence est attribuée à des éléments sur la plateforme.',
            );
        } else {
            $entityManager->remove($skill);
            $entityManager->flush();

            $this->addFlash(
                'success',
                'Compétence supprimée avec succès',
            );
        }

        return $this->redirectToRoute('admin_parameters_index-skills');
    }
}
