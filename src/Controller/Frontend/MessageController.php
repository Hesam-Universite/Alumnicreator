<?php

namespace App\Controller\Frontend;

use App\Entity\Conversation;
use App\Entity\Message;
use App\Entity\Parameter;
use App\Entity\User;
use App\Entity\UserConversation;
use App\Form\MessageType;
use App\Repository\ParameterRepository;
use App\Repository\UserConversationRepository;
use App\Repository\UserGroupRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\NonUniqueResultException;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/messages', name: 'message_')]
#[Security('is_granted("ROLE_STUDENT") or is_granted("ROLE_COMPANY") or is_granted("ROLE_ADMIN")')]
class MessageController extends AbstractController
{
    /**
     * Conversation between two users and sending new messages.
     *
     * @param User                       $recepient
     * @param ParameterRepository        $parameterRepository
     * @param Request                    $request
     * @param EntityManagerInterface     $entityManager
     * @param UserConversationRepository $userConversationRepository
     * @param MailerInterface            $mailer
     *
     * @return Response - frontend/message/new.html.twig
     *
     * @throws NonUniqueResultException
     * @throws TransportExceptionInterface
     */
    #[Route('/nouveau/{id<\d+>}', name: 'new')]
    public function new(User $recepient, ParameterRepository $parameterRepository, Request $request, EntityManagerInterface $entityManager, UserConversationRepository $userConversationRepository, MailerInterface $mailer): Response
    {
        // User can't send a message to himself
        if ($this->getUser() === $recepient) {
            $this->addFlash(
                'error',
                'Vous ne pouvez pas envoyer un message à vous-même.');

            return $this->redirectToRoute('resume_all');
        }

        // A company can't send a message to another company
        if (in_array('ROLE_COMPANY', $this->getUser()->getRoles()) && in_array('ROLE_COMPANY', $recepient->getRoles())) {
            $this->addFlash(
                'error',
                'Vous ne pouvez pas contacter une autre entreprise.');

            return $this->redirectToRoute('resume_all');
        }

        $alwaysHaveAConversation = false;
        $conversationBetweenBothUsers = new Conversation();

        // Get conversations of connected user
        $connectedUserConversations = $userConversationRepository->findBy(['user' => $this->getUser()]);

        // Check if the second user has a common conversation
        $commonConversations = [];
        foreach ($connectedUserConversations as $connectedUserConversation) {
            if (null !== $userConversationRepository->findOneBy(['user' => $recepient, 'conversation' => $connectedUserConversation->getConversation()])) {
                $commonConversations[] = $userConversationRepository->findOneBy(['user' => $recepient, 'conversation' => $connectedUserConversation->getConversation()]);
            }
        }

        // Check if one of the common conversations have exactly two users (we have to verify this because of conversations groups)
        foreach ($commonConversations as $conversation) {
            if ($userConversationRepository->countByConversation($conversation->getConversation())[1] === 2) {
                $conversationBetweenBothUsers = $conversation->getConversation();
                $alwaysHaveAConversation = true;

                // Actualize the last visit of the connected user
                $connectedUserConversation = $userConversationRepository->findOneBy(['user' => $this->getUser(), 'conversation' => $conversation->getConversation()])
                    ->setLastVisit(new \DateTime());
                $entityManager->persist($connectedUserConversation);
                $entityManager->flush();

                $otherUserConversation = $userConversationRepository->findOneBy(['user' => $recepient, 'conversation' => $conversation->getConversation()])
                    ->setLastNotification(new \DateTime());
                break;
            }
        }

        if (false === $alwaysHaveAConversation) {
            // We create a relation between User and Conversation for the connected User
            $connectedUserConversation = (new UserConversation())
                ->setUser($this->getUser())
                ->setLastVisit(new \DateTime())
                ->setConversation($conversationBetweenBothUsers);
            // We create a relation between User and Conversation for the other User
            $otherUserConversation = (new UserConversation())
                ->setUser($recepient)
                ->setLastNotification(new \DateTime())
                ->setConversation($conversationBetweenBothUsers);
        }

        $message = (new Message())
            ->setAuthor($this->getUser())
            ->setSendingTime(new \DateTime())
            ->setConversation($conversationBetweenBothUsers);

        $form = $this->createForm(MessageType::class, $message);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $conversationBetweenBothUsers->setLastMessage($message->getSendingTime());
            $entityManager->persist($conversationBetweenBothUsers);
            $entityManager->persist($connectedUserConversation);
            $entityManager->persist($otherUserConversation);
            $entityManager->persist($message);
            $entityManager->flush();

            if ($otherUserConversation->getUser()->receiveMessageNotificationsByEmail()) {
                if (in_array('ROLE_COMPANY', $connectedUserConversation->getUser()->getRoles())) {
                    $username = $connectedUserConversation->getUser()->getCompanyName();
                } else {
                    $username = $connectedUserConversation->getUser()->getFirstname().' '.$connectedUserConversation->getUser()->getName();
                }
                $emailFrom = $parameterRepository->findOneBy(['code' => Parameter::SMTP_FROM])->getValue();
                $notification = (new Email())
                    ->from($emailFrom)
                    ->to($otherUserConversation->getUser()->getEmail())
                    ->subject('Vous avez reçu un nouveau message | Alumni Creator')
                    ->html('<h1>Vous avez reçu un nouveau message de '.$username.'</h1><p>Contenu du message :</p><p>'.$form->getData()->getContent().'</p>');
                $mailer->send($notification);
            }

            return $this->redirectToRoute('message_new', ['id' => $recepient->getId()]);
        }

        return $this->render('frontend/message/new.html.twig', [
            'form' => $form->createView(),
            'conversationBetweenBothUsers' => $conversationBetweenBothUsers,
            'connectedUser' => $this->getUser(),
            'otherUser' => $recepient,
        ]);
    }

    /**
     * @throws NonUniqueResultException
     */
    #[Route('/mes-conversations', name: 'index')]
    public function index(UserConversationRepository $userConversationRepository, UserGroupRepository $userGroupRepository)
    {
        $userConversations = [];
        $userConversationsConnectedUser = [];

        $connectedUserConversations = $userConversationRepository->findBy(['user' => $this->getUser()]);

        foreach ($connectedUserConversations as $UserConversation) {
            // Check if one of the conversations have exactly two users (we have to verify this because of conversations groups)
            if ($userConversationRepository->countByConversation($UserConversation->getConversation())[1] === 2) {
                $userConversations[] = $userConversationRepository->findOneByIdAndUserIsNotUserLogged($UserConversation->getConversation(), $this->getUser());
                $userConversationsConnectedUser[] = $userConversationRepository->findOneBy(['conversation' => $UserConversation->getConversation(), 'user' => $this->getUser()]);
            }
        }

        $invitationsPending = $userGroupRepository->findBy(['user' => $this->getUser(), 'acceptedTheInvitation' => false]);

        return $this->render('frontend/message/index.html.twig', [
            'userConversations' => $userConversations, // This will be used to find other users
            'userConversationsConnectedUser' => $userConversationsConnectedUser, // And this one will be used to get information about notifications of the connected user
            'invitationsPending' => $invitationsPending,
        ]);
    }
}
