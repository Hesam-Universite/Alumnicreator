<?php

namespace App\Command;

use App\Entity\DirectoryPageInstance;
use App\Entity\JobInstance;
use App\Entity\ResumeInstance;
use App\Entity\UserInstance;
use App\Repository\DirectoryPageInstanceRepository;
use App\Repository\InstanceRepository;
use App\Repository\JobInstanceRepository;
use App\Repository\ResumeInstanceRepository;
use App\Repository\UserInstanceRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Uid\Uuid;
use Symfony\Contracts\HttpClient\HttpClientInterface;

#[AsCommand(
    name: 'cron:getdata',
    description: 'Command to get data from the other instances',
)]
class CronGetDataCommand extends Command
{
    public function __construct(
        private InstanceRepository $instanceRepository,
        private HttpClientInterface $client,
        private ResumeInstanceRepository $resumeInstanceRepository,
        private JobInstanceRepository $jobInstanceRepository,
        private UserInstanceRepository $userInstanceRepository,
        private DirectoryPageInstanceRepository $directoryPageInstanceRepository,
        private EntityManagerInterface $entityManager,
    ) {
        parent::__construct();
    }

    public function execute(InputInterface $input, OutputInterface $output): int
    {
        $everyDataUrl = ['get-jobs', 'get-resumes', 'get-companies', 'get-students'];

        $validInstances = $this->instanceRepository->findBy(['allowOtherInstance' => true]);

        foreach ($validInstances as $instance) {
            foreach ($everyDataUrl as $dataUrl) {
                $response = $this->client->request('GET', $instance->getInstanceUrl().'/api/'.$dataUrl, [
                    'json' => [
                        'localId' => $instance->getLocalId(),
                        'externalId' => $instance->getExternalId(),
                    ],
                ]);
                $contents = $response->toArray();
                foreach ($contents as $key => $content) {
                    if ($dataUrl === 'get-resumes') {
                        if ($contents['allowedGetResumes'] === false) {
                            $resumes = $this->resumeInstanceRepository->findBy(['instanceId' => $instance->getExternalId()]);
                            foreach ($resumes as $resume) {
                                $this->entityManager->remove($resume);
                            }
                        } elseif ('allowedGetResumes' !== $key) {
                            $resume = $this->resumeInstanceRepository->findOneBy(['otherInstanceId' => $content['id'], 'instanceId' => $instance->getExternalId()]);
                            if (null == $resume) {
                                $resume = new ResumeInstance();
                            }
                            $birthday = new \DateTime();
                            $birthday->setTimestamp(strtotime($content['birthday']));
                            $resume->setOtherInstanceId($content['id'])
                                ->setInstanceId(Uuid::fromString($content['uuid']))
                                ->setFirstname($content['firstname'])
                                ->setLastname($content['lastname'])
                                ->setEmail($content['email'])
                                ->setImageUrl($instance->getInstanceUrl().'/images/utilisateurs/'.$content['imageUrl'])
                                ->setPresentation($content['presentation'])
                                ->setSkill($content['skill'])
                                ->setActivityArea($content['activityArea'])
                                ->setActivityAreaOther($content['activityAreaOther'])
                                ->setBirthday($birthday)
                                ->setUserPostalCode($content['userPostalCode'])
                                ->setStatus($content['status'])
                                ->setUserId($content['userId'])
                                ->setResumeName($content['resumeName'])
                                ->setAdditionalFileName($content['additionalFileName'])
                            ;
                            $this->entityManager->persist($resume);
                        }
                    }

                    if ($dataUrl === 'get-jobs') {
                        if ($contents['allowedGetJobs'] === false) {
                            $jobs = $this->jobInstanceRepository->findBy(['instanceId' => $instance->getExternalId()]);
                            foreach ($jobs as $job) {
                                $this->entityManager->remove($job);
                            }
                        } elseif ('allowedGetJobs' !== $key) {
                            $job = $this->jobInstanceRepository->findOneBy(['otherInstanceId' => $content['id'], 'instanceId' => $instance->getExternalId()]);
                            if (null == $job) {
                                $job = new JobInstance();
                            }
                            $job->setOtherInstanceId($content['id'])
                                ->setInstanceId(Uuid::fromString($content['uuid']))
                                ->setTitle($content['title'])
                                ->setCompanyPresentation($content['companyPresentation'])
                                ->setDescription($content['description'])
                                ->setActivityArea($content['activityArea'])
                                ->setDesiredLevel($content['desiredLevel'])
                                ->setTypeOfContract($content['typeOfContract'])
                                ->setCity($content['city'])
                                ->setRemuneration($content['remuneration'])
                                ->setContactEmail($content['contactEmail'])
                                ->setLinkToTheJobOffer($content['linkToTheJobOffer'])
                                ->setStartDate(new \DateTime($content['startDate']))
                                ->setDeadlineJobOffer(new \DateTime($content['deadlineJobOffer']))
                                ->setCreationDate(new \DateTime($content['creationDate']))
                                ->setPostalCode($content['postalCode'])
                                ->setPictureName($content['pictureName'])
                                ->setCompanyName($content['companyName'])
                                ->setFirstname($content['firstname'])
                                ->setLastname($content['lastname'])
                                ->setAuthorId($content['authorId']);
                            $this->entityManager->persist($job);
                        }
                    }

                    if ($dataUrl === 'get-companies') {
                        if ($contents['allowedGetCompanies'] === false) {
                            $companies = $this->userInstanceRepository->findBy(['instanceId' => $instance->getExternalId()]);
                            foreach ($companies as $company) {
                                $this->entityManager->remove($company);
                            }
                        } elseif ('allowedGetCompanies' !== $key) {
                            $company = $this->userInstanceRepository->findOneBy(['otherInstanceId' => $content['id'], 'instanceId' => $instance->getExternalId()]);
                            if (null == $company) {
                                $company = new UserInstance();
                            }
                            $company->setOtherInstanceId($content['id'])
                                ->setInstanceId(Uuid::fromString($content['uuid']))
                                ->setRoles($content['roles'])
                                ->setEmail($content['email'])
                                ->setName($content['name'])
                                ->setFirstname($content['firstname'])
                                ->setPhone($content['phone'])
                                ->setActivityArea($content['activityArea'])
                                ->setSiret($content['siret'])
                                ->setCompanyName($content['companyName'])
                                ->setRoleInTheCompany($content['roleInTheCompany'])
                                ->setCompanyAddress($content['companyAddress'])
                                ->setActivityAreaOther($content['ActivityAreaOther'])
                                ->setRegistrationDate(new \DateTime($content['registrationDate']))
                                ->setPictureName($content['pictureName'])
                                ->setPostalCode($content['postalCode'])
                                ->setCity($content['city'])
                                ->setLinkedinLink($content['linkedinLink']);
                            $this->entityManager->persist($company);
                        }
                    }

                    if ($dataUrl === 'get-students') {
                        if ($contents['allowedGetStudents'] === false) {
                            $students = $this->directoryPageInstanceRepository->findBy(['instanceId' => $instance->getExternalId()]);
                            foreach ($students as $student) {
                                if (null !== $student->getUserId()) {
                                    $user = $this->userInstanceRepository->findOneBy(['otherInstanceId' => $student->getUserId(), 'instanceId' => $instance->getExternalId()]);
                                    if ($user !== null) {
                                        $this->entityManager->remove($user);
                                    }
                                }
                                $this->entityManager->remove($student);
                            }
                        } elseif ('allowedGetStudents' !== $key) {
                            $student = $this->directoryPageInstanceRepository->findOneBy(['otherInstanceId' => $content['id'], 'instanceId' => $instance->getExternalId()]);
                            if (null == $student) {
                                $student = new DirectoryPageInstance();
                            }
                            $student->setOtherInstanceId($content['id'])
                                ->setInstanceId(Uuid::fromString($content['uuid']))
                                ->setLastname($content['lastname'])
                                ->setFirstname($content['firstname'])
                                ->setClass($content['class'])
                                ->setEmail($content['email'])
                                ->setLinkedinLink($content['linkedinLink']);

                            if (null !== $content['userId']) {
                                $user = $this->userInstanceRepository->findOneBy(['otherInstanceId' => $content['userId'], 'instanceId' => $instance->getExternalId()]);
                                if (null == $user) {
                                    $user = new UserInstance();
                                }
                                $user->setOtherInstanceId($content['userId'])
                                    ->setInstanceId(Uuid::fromString($content['uuid']))
                                    ->setEmail($content['userEmail'])
                                    ->setRoles($content['userRoles'])
                                    ->setName($content['userName'])
                                    ->setFirstname($content['userFirstname'])
                                    ->setPhone($content['userPhone'])
                                    ->setBirthday(new \DateTime($content['userBirthday']))
                                    ->setAddress($content['userAddress'])
                                    ->setClass($content['userClass'])
                                    ->setTraining($content['userTraining'])
                                    ->setPersonalLink($content['userPersonalLink'])
                                    ->setRegistrationDate(new \DateTime($content['userRegistrationDate']))
                                    ->setPictureName($content['userPictureName'])
                                    ->setPostalCode($content['userPostalCode'])
                                    ->setCity($content['userCity'])
                                    ->setLinkedinLink($content['userLinkedinLink']);
                                $student->setUserId($content['userId']);
                                $this->entityManager->persist($user);
                            }
                            $this->entityManager->persist($student);
                        }
                    }
                }
            }
            $this->entityManager->flush();
        }

        return Command::SUCCESS;
    }
}
