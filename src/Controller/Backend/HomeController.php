<?php

namespace App\Controller\Backend;

use App\Repository\ConversationRepository;
use App\Repository\JobRepository;
use App\Repository\MessageRepository;
use App\Repository\UserRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

#[Security('is_granted("ROLE_ADMIN")')]
#[Route('/administration', name: 'admin_')]
class HomeController extends AbstractController
{
    #[Route('/', name: 'index')]
    public function index(MessageRepository $messageRepository, ConversationRepository $conversationRepository, UserRepository $userRepository, JobRepository $jobRepository)
    {
        $messages = count($messageRepository->findAll());
        $conversations = count($conversationRepository->findAll());
        $numberUsersWhoSentMessages = $messageRepository->countUsers();

        $jobs = $jobRepository->findAll();
        $students = $userRepository->findByRole('ROLE_STUDENT');
        $companies = $userRepository->findByRole('ROLE_COMPANY');

        return $this->render('backend/home.html.twig', [
            'numberOfMessages' => $messages,
            'numberOfConversations' => $conversations,
            'numberUsersWhoSentMessages' => $numberUsersWhoSentMessages[1],
            'numberOfJobs' => count($jobs),
            'numberOfStudents' => count($students),
            'numberOfCompanies' => count($companies),
        ]);
    }
}
