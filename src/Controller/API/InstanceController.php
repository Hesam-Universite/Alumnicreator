<?php

namespace App\Controller\API;

use App\Repository\DirectoryPageInstanceRepository;
use App\Repository\DirectoryPageRepository;
use App\Repository\InstanceRepository;
use App\Repository\JobInstanceRepository;
use App\Repository\JobRepository;
use App\Repository\ResumeInstanceRepository;
use App\Repository\ResumeRepository;
use App\Repository\UserInstanceRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use FOS\RestBundle\Controller\Annotations as Rest;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class InstanceController extends AbstractController
{
    #[Rest\Post('/api/connect-instance')]
    public function connect(Request $request, InstanceRepository $instanceRepository, EntityManagerInterface $entityManager)
    {
        $instance = $instanceRepository->findOneBy(['localId' => $request->get('externalId')]);

        if (null === $instance) {
            return new JsonResponse(['message' => 'Erreur : ID distant invalide.'], Response::HTTP_NOT_FOUND);
        }

        if (null == $instance->getExternalId()) {
            return new JsonResponse(['message' => 'L\'instance distante doit entrer votre ID pour terminer le processus.'], Response::HTTP_OK);
        }

        if ($instance->getExternalId() == $request->get('localId')) {
            $instance->setAllowOtherInstance(true);
            $entityManager->persist($instance);
            $entityManager->flush();

            return $this->json($instance, Response::HTTP_OK);
        }

        return new JsonResponse(['message' => 'Une erreur est survenue.'], Response::HTTP_INTERNAL_SERVER_ERROR);
    }

    #[Rest\Post('/api/disconnect-instance')]
    public function disconnect(Request $request, InstanceRepository $instanceRepository, EntityManagerInterface $entityManager, DirectoryPageInstanceRepository $directoryPageInstanceRepository, JobInstanceRepository $jobInstanceRepository, ResumeInstanceRepository $resumeInstanceRepository, UserInstanceRepository $userInstanceRepository)
    {
        $instance = $instanceRepository->findOneBy(['localId' => $request->get('externalId')]);

        if (null === $instance) {
            return new JsonResponse(['message' => 'Erreur : ID distant invalide.'], Response::HTTP_NOT_FOUND);
        }

        if ($instance->getExternalId() == $request->get('localId')) {
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

            $entityManager->remove($instance);
            $entityManager->flush();

            return $this->json($instance, Response::HTTP_OK);
        }

        return new JsonResponse(['message' => 'Une erreur est survenue.'], Response::HTTP_INTERNAL_SERVER_ERROR);
    }

    #[Rest\Get('/api/get-resumes')]
    public function getResumes(Request $request, InstanceRepository $instanceRepository, ResumeRepository $resumeRepository): Response
    {
        $parameter = json_decode($request->getContent(), true);

        $instance = $instanceRepository->findOneBy(['localId' => $parameter['externalId']]);

        if (null === $instance) {
            return new JsonResponse(['message' => 'Erreur : ID distant invalide.'], Response::HTTP_NOT_FOUND);
        }

        if (null == $instance->getExternalId()) {
            return new JsonResponse(['message' => 'L\'instance distante doit entrer votre ID pour terminer le processus.'], Response::HTTP_OK);
        }

        $resumes = $resumeRepository->findAll();

        if ($instance->getExternalId() == $parameter['localId']) {
            if ($instance->isAllowShareResumes()) {
                $resumesFormatted = ['allowedGetResumes' => true];
                foreach ($resumes as $resume) {
                    $resumesFormatted[] = [
                        'id' => $resume->getId(),
                        'uuid' => $instance->getLocalId(),
                        'firstname' => $resume->getUser()->getFirstname(),
                        'lastname' => $resume->getUser()->getName(),
                        'imageUrl' => $resume->getUser()->getPictureName(),
                        'email' => $resume->getUser()->getEmail(),
                        'presentation' => $resume->getPresentation(),
                        'skill' => $resume->getSkill()->getName(),
                        'activityArea' => $resume->getActivityArea()->getName(),
                        'activityAreaOther' => $resume->getActivityAreaOther(),
                        'birthday' => $resume->getUser()->getBirthday(),
                        'userPostalCode' => $resume->getUser()->getPostalCode(),
                        'status' => $resume->getStatus(),
                        'userId' => $resume->getUser()->getId(),
                        'resumeName' => $resume->getResumeName(),
                        'additionalFileName' => $resume->getAdditionalFileName(),
                    ];
                }

                return $this->json($resumesFormatted, Response::HTTP_OK);
            }
        }

        $resumesFormatted = ['allowedGetResumes' => false];

        return $this->json($resumesFormatted, Response::HTTP_OK);
    }

    #[Rest\Get('/api/get-jobs')]
    public function getJobs(Request $request, InstanceRepository $instanceRepository, JobRepository $jobRepository): Response
    {
        $parameter = json_decode($request->getContent(), true);

        $instance = $instanceRepository->findOneBy(['localId' => $parameter['externalId']]);

        if (null === $instance) {
            return new JsonResponse(['message' => 'Erreur : ID distant invalide.'], Response::HTTP_NOT_FOUND);
        }

        if (null == $instance->getExternalId()) {
            return new JsonResponse(['message' => 'L\'instance distante doit entrer votre ID pour terminer le processus.'], Response::HTTP_OK);
        }

        $jobs = $jobRepository->findAll();

        if ($instance->getExternalId() == $parameter['localId']) {
            if ($instance->isAllowShareJobs()) {
                $jobsFormatted = ['allowedGetJobs' => true];
                foreach ($jobs as $job) {
                    if ($job->isApproved()) {
                        $jobsFormatted[] = [
                            'id' => $job->getId(),
                            'uuid' => $instance->getLocalId(),
                            'title' => $job->getTitle(),
                            'companyPresentation' => $job->getCompanyPresentation(),
                            'description' => $job->getDescription(),
                            'activityArea' => $job->getActivityArea()->getName(),
                            'desiredLevel' => $job->getDesiredLevel(),
                            'typeOfContract' => $job->getTypeOfContract(),
                            'city' => $job->getCity(),
                            'remuneration' => $job->getRemuneration(),
                            'contactEmail' => $job->getContactEmail(),
                            'linkToTheJobOffer' => $job->getLinkToTheJobOffer(),
                            'startDate' => $job->getStartDate(),
                            'deadlineJobOffer' => $job->getDeadlineJobOffer(),
                            'creationDate' => $job->getCreationDate(),
                            'postalCode' => $job->getPostalCode(),
                            'pictureName' => $job->getAuthor()->getPictureName(),
                            'companyName' => $job->getAuthor()->getCompanyName(),
                            'firstname' => $job->getAuthor()->getFirstname(),
                            'lastname' => $job->getAuthor()->getName(),
                            'authorId' => $job->getAuthor()->getId(),
                        ];
                    }
                }

                return $this->json($jobsFormatted, Response::HTTP_OK);
            }
        }

        $jobsFormatted = ['allowedGetJobs' => false];

        return $this->json($jobsFormatted, Response::HTTP_OK);
    }

    #[Rest\Get('/api/get-companies')]
    public function getCompanies(Request $request, InstanceRepository $instanceRepository, UserRepository $userRepository): Response
    {
        $parameter = json_decode($request->getContent(), true);

        $instance = $instanceRepository->findOneBy(['localId' => $parameter['externalId']]);

        if (null === $instance) {
            return new JsonResponse(['message' => 'Erreur : ID distant invalide.'], Response::HTTP_NOT_FOUND);
        }

        if (null == $instance->getExternalId()) {
            return new JsonResponse(['message' => 'L\'instance distante doit entrer votre ID pour terminer le processus.'], Response::HTTP_OK);
        }

        $companies = $userRepository->findByRoleAndApproved('ROLE_COMPANY');

        if ($instance->getExternalId() == $parameter['localId']) {
            if ($instance->isAllowShareCompanies()) {
                $companiesFormatted = ['allowedGetCompanies' => true];
                foreach ($companies as $company) {
                    $companiesFormatted[] = [
                        'id' => $company->getId(),
                        'roles' => $company->getRoles(),
                        'uuid' => $instance->getLocalId(),
                        'email' => $company->getEmail(),
                        'name' => $company->getName(),
                        'firstname' => $company->getFirstname(),
                        'phone' => $company->getPhone(),
                        'activityArea' => $company->getActivityArea()->getName(),
                        'siret' => $company->getSiret(),
                        'companyName' => $company->getCompanyName(),
                        'roleInTheCompany' => $company->getRoleInTheCompany(),
                        'companyAddress' => $company->getAddress(),
                        'ActivityAreaOther' => $company->getActivityAreaOther(),
                        'registrationDate' => $company->getRegistrationDate(),
                        'pictureName' => $company->getPictureName(),
                        'postalCode' => $company->getPostalCode(),
                        'city' => $company->getCity(),
                        'linkedinLink' => $company->getLinkedinLink(),
                    ];
                }

                return $this->json($companiesFormatted, Response::HTTP_OK);
            }
        }

        $companiesFormatted = ['allowedGetCompanies' => false];

        return $this->json($companiesFormatted, Response::HTTP_OK);
    }

    #[Rest\Get('/api/get-students')]
    public function getStudents(Request $request, InstanceRepository $instanceRepository, DirectoryPageRepository $directoryPageRepository): Response
    {
        $parameter = json_decode($request->getContent(), true);

        $instance = $instanceRepository->findOneBy(['localId' => $parameter['externalId']]);

        if (null === $instance) {
            return new JsonResponse(['message' => 'Erreur : ID distant invalide.'], Response::HTTP_NOT_FOUND);
        }

        if (null == $instance->getExternalId()) {
            return new JsonResponse(['message' => 'L\'instance distante doit entrer votre ID pour terminer le processus.'], Response::HTTP_OK);
        }

        $students = $directoryPageRepository->findBy([], ['lastname' => 'ASC']);

        if ($instance->getExternalId() == $parameter['localId']) {
            if ($instance->isAllowShareStudents()) {
                $studentsFormatted = ['allowedGetStudents' => true];
                foreach ($students as $student) {
                    if (null !== $student->getUser()) {
                        $studentsFormatted[] = [
                            'id' => $student->getId(),
                            'uuid' => $instance->getLocalId(),
                            'lastname' => $student->getLastname(),
                            'firstname' => $student->getFirstname(),
                            'class' => $student->getClass(),
                            'email' => $student->getEmail(),
                            'linkedinLink' => $student->getLinkedinLink(),
                            'userId' => $student->getUser()->getId(),
                            'userEmail' => $student->getUser()->getEmail(),
                            'userRoles' => $student->getUser()->getRoles(),
                            'userStatus' => $student->getUser()->getStatus(),
                            'userName' => $student->getUser()->getName(),
                            'userFirstname' => $student->getUser()->getFirstname(),
                            'userPhone' => $student->getUser()->getPhone(),
                            'userBirthday' => $student->getUser()->getBirthday(),
                            'userAddress' => $student->getUser()->getAddress(),
                            'userClass' => $student->getUser()->getClass(),
                            'userTraining' => $student->getUser()->getTraining(),
                            'userPersonalLink' => $student->getUser()->getPersonalLink(),
                            'userRegistrationDate' => $student->getUser()->getRegistrationDate(),
                            'userPictureName' => $student->getUser()->getPictureName(),
                            'userPostalCode' => $student->getUser()->getPostalCode(),
                            'userCity' => $student->getUser()->getCity(),
                            'userLinkedinLink' => $student->getUser()->getLinkedinLink(),
                        ];
                    } else {
                        $studentsFormatted[] = [
                            'id' => $student->getId(),
                            'uuid' => $instance->getLocalId(),
                            'lastname' => $student->getLastname(),
                            'firstname' => $student->getFirstname(),
                            'class' => $student->getClass(),
                            'email' => $student->getEmail(),
                            'linkedinLink' => $student->getLinkedinLink(),
                            'userId' => null,
                        ];
                    }
                }

                return $this->json($studentsFormatted, Response::HTTP_OK);
            }
        }

        $studentsFormatted = ['allowedGetStudents' => false];

        return $this->json($studentsFormatted, Response::HTTP_OK);
    }
}
