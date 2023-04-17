<?php

namespace App\Controller\Frontend;

use App\Entity\Article;
use App\Entity\Event;
use App\Entity\Group;
use App\Entity\MediaGroup;
use App\Entity\User;
use App\Entity\UserGroup;
use App\Enum\RoleGroup;
use App\Enum\StatusArticle;
use App\Form\AddUserToAGroupType;
use App\Form\ArticleGroupType;
use App\Form\EventType;
use App\Form\GroupType;
use App\Form\MediaGroupType;
use App\Form\TransferGroup;
use App\Repository\ArticleRepository;
use App\Repository\EventRepository;
use App\Repository\GroupRepository;
use App\Repository\MediaGroupRepository;
use App\Repository\UserGroupRepository;
use App\Repository\UserRepository;
use App\Service\CheckAuthorisationGroupService;
use App\Service\DeleteGroupService;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Security("is_granted('ROLE_ALUMNI') or is_granted('ROLE_COMPANY') or is_granted('ROLE_ADMIN')")]
#[Route('/groupes', name: 'group_')]
class GroupController extends AbstractController
{
    /**
     * to see all groups.
     *
     * @param GroupRepository $groupRepository
     *
     * @return Response - frontend/group/index.html.twig
     */
    #[Route('/', name: 'index')]
    public function index(GroupRepository $groupRepository, UserGroupRepository $userGroupRepository): Response
    {
        $allGroupsUser = $userGroupRepository->findBy(['user' => $this->getUser(), 'acceptedTheInvitation' => true]);
        $allGroups = $groupRepository->findBy(['isApproved' => true, 'isActive' => true, 'visibility' => 1]);

        $groupsUserToDisplay = [];
        foreach ($allGroupsUser as $groupUser) {
            if ($groupUser->getUserGroup()->isApproved() && ($groupUser->getUserGroup()->isActive() || 1 === $groupUser->getRoleInGroup()->value || 2 === $groupUser->getRoleInGroup()->value)) {
                $groupsUserToDisplay[] = $groupUser->getUserGroup();
            }
        }

        foreach ($groupsUserToDisplay as $groupUserToDisplay) {
            foreach ($allGroups as $key => $group) {
                if ($groupUserToDisplay->getId() === $group->getId()) {
                    unset($allGroups[$key]);
                }
            }
        }

        return $this->render('frontend/group/index.html.twig', [
            'groupsUser' => $groupsUserToDisplay,
            'groups' => $allGroups,
        ]);
    }

    /**
     * to handle a group.
     *
     * @param Group|null             $group
     * @param Request                $request
     * @param EntityManagerInterface $entityManager
     * @param UserGroupRepository    $userGroupRepository
     *
     * @return Response - frontend/group/handle.html.twig
     */
    #[Route('/modifier/{id<\d+>}', name: 'edit')]
    #[Route('/nouveau', name: 'new')]
    public function handle(Group $group = null, Request $request, EntityManagerInterface $entityManager, UserGroupRepository $userGroupRepository, CheckAuthorisationGroupService $checkAuthorisationGroupService): Response
    {
        $connectedUserInGroup = null;
        if (null === $group) {
            $group = new Group();
            if (!in_array('ROLE_ALUMNI', $this->getUser()->getRoles()) && !$this->isGranted('ROLE_ADMIN')) {
                $this->addFlash(
                    'error',
                    'Vous n\'êtes pas autorisé(e) à créer un nouveau groupe.',
                );

                return $this->redirectToRoute('group_index');
            }
        } else {
            $connectedUserInGroup = $userGroupRepository->findOneBy(['userGroup' => $group, 'user' => $this->getUser(), 'acceptedTheInvitation' => true]);
            if (!$this->isGranted('ROLE_ADMIN')) {
                if ($checkAuthorisationGroupService->isInStaffAndGroupIsApproved($this->getUser(), $group) === false) {
                    $this->addFlash(
                        'error',
                        'Vous n\'êtes pas autorisé(e) à faire des modifications dans ce groupe.'
                    );

                    return $this->redirectToRoute('group_index');
                }
            }
        }

        $form = $this->createForm(GroupType::class, $group);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            if (null === $group->getId()) {
                $group->setIsApproved(false);
                $group->setIsActive(true);
                $group->setApprovalNotificationSent(false);

                $this->addFlash(
                    'success',
                    'Groupe créé avec succès. Il est en attente de validation par un administrateur.'
                );

                $userGroup = (new UserGroup())
                    ->setUser($this->getUser())
                    ->setUserGroup($group)
                    ->setRoleInGroup(RoleGroup::ADMIN)
                    ->setAcceptedTheInvitation(true)
                ;

                $entityManager->persist($userGroup);
            } else {
                $this->addFlash(
                    'success',
                    'Groupe modifié avec succès'
                );
            }

            $entityManager->persist($group);
            $entityManager->flush();

            return $this->redirectToRoute('group_index');
        }

        return $this->render('frontend/group/handle.html.twig', [
            'form' => $form->createView(),
            'group' => $group,
            'connectedUserInGroup' => $connectedUserInGroup,
        ]);
    }

    /**
     * display one group.
     *
     * @param Group               $group
     * @param UserGroupRepository $userGroupRepository
     *
     * @return Response - frontend/group/one.html.twig
     */
    #[Route('/{id<\d+>}', name: 'one')]
    public function one(Group $group, UserGroupRepository $userGroupRepository, EventRepository $eventRepository, ArticleRepository $articleRepository, MediaGroupRepository $mediaGroupRepository): Response
    {
        $connectedUserInGroup = $userGroupRepository->findOneBy(['user' => $this->getUser(), 'userGroup' => $group, 'acceptedTheInvitation' => true]);

        if (!$this->isGranted('ROLE_ADMIN')) {
            if (2 === $group->getVisibility()->value && null === $connectedUserInGroup) {
                $this->addFlash(
                    'error',
                    'Ce groupe est privé.'
                );

                return $this->redirectToRoute('group_index');
            }

            if (!$group->isApproved()) {
                $this->addFlash(
                    'error',
                    'Ce groupe n\'est pas actif.'
                );

                return $this->redirectToRoute('group_index');
            }
            if (!$group->isActive() && !($connectedUserInGroup->getRoleInGroup()->value === 1 || $connectedUserInGroup->getRoleInGroup()->value === 2)) {
                $this->addFlash(
                    'error',
                    'Ce groupe n\'est pas actif.'
                );

                return $this->redirectToRoute('group_index');
            }
        }

        $staffOfTheGroup = $userGroupRepository->findBy(['userGroup' => $group, 'roleInGroup' => [1, 2], 'acceptedTheInvitation' => true], ['roleInGroup' => 'ASC']);
        $members = $userGroupRepository->findBy(['userGroup' => $group, 'roleInGroup' => 3, 'acceptedTheInvitation' => true]);

        $events = $eventRepository->findBy(['groupEvent' => $group]);

        $articles = $articleRepository->findLastThreeArticlesOfAGroup($group);

        $mediasGroup = $mediaGroupRepository->findBy(['mediaGroup' => $group], ['id' => 'DESC'], 4);

        return $this->render('frontend/group/one.html.twig', [
            'group' => $group,
            'connectedUserInGroup' => $connectedUserInGroup,
            'staffOfTheGroup' => $staffOfTheGroup,
            'members' => $members,
            'events' => $events,
            'articles' => $articles,
            'mediasGroup' => $mediasGroup,
        ]);
    }

    /**
     * to delete a group.
     *
     * @param Group $group
     * @param UserGroupRepository $userGroupRepository
     * @param DeleteGroupService $deleteGroupService
     *
     * @return Response
     */
    #[Route('/supprimer/{id<\d+>}', name: 'delete')]
    public function delete(Group $group, UserGroupRepository $userGroupRepository, DeleteGroupService $deleteGroupService): Response
    {
        $connectedUserInGroup = $userGroupRepository->findOneBy(['userGroup' => $group, 'user' => $this->getUser(), 'acceptedTheInvitation' => true]);

        if (!$this->isGranted('ROLE_ADMIN')) {
            if ($connectedUserInGroup->getRoleInGroup()->value !== 1) {
                $this->addFlash(
                    'error',
                    'Vous n\'êtes pas autorisé(e) à supprimer ce groupe.'
                );

                return $this->redirectToRoute('group_index');
            }
        }

        $deleteGroupService->deleteGroup($group);

        $this->addFlash(
            'success',
            'Groupe supprimé avec succès.'
        );

        return $this->redirectToRoute('group_index');
    }

    /**
     * Add a new member to a group.
     *
     * @param Group                  $group
     * @param EntityManagerInterface $entityManager
     * @param UserGroupRepository    $userGroupRepository
     * @param Request                $request
     * @param UserRepository         $userRepository
     *
     * @return RedirectResponse|Response - frontend/group/add-member.html.twig
     */
    #[Route('/{id<\d+>}/nouveau-membre', name: 'new-member')]
    public function addANewMember(Group $group, EntityManagerInterface $entityManager, UserGroupRepository $userGroupRepository, Request $request, UserRepository $userRepository, CheckAuthorisationGroupService $checkAuthorisationGroupService): Response
    {
        if ($checkAuthorisationGroupService->isInStaffAndGroupIsApprovedAndActive($this->getUser(), $group) === false) {
            $this->addFlash(
                'error',
                'Vous n\'êtes pas autorisé(e) à ajouter un membre à ce groupe.'
            );

            return $this->redirectToRoute('group_index');
        }

        $form = $this->createForm(AddUserToAGroupType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $userToAdd = $userRepository->findOneBy(['email' => $form->getData()['user'], 'isApproved' => true]);

            $userGroup = $userGroupRepository->findOneBy(['user' => $userToAdd, 'userGroup' => $group]);

            if (null === $userToAdd) {
                $this->addFlash(
                    'error',
                    'Cet utilisateur n\'existe pas. Précisions : veuillez vous assurer d\'indiquer le mail lié au compte de l\'utilisateur. Ce dernier peut être différent de celui affiché dans l\'annuaire.'
                );

                return $this->redirectToRoute('group_new-member', ['id' => $group->getId()]);
            }

            if (null !== $userGroup) {
                $this->addFlash(
                    'error',
                    'Cet utilisateur a déjà été ajouté.'
                );

                return $this->redirectToRoute('group_new-member', ['id' => $group->getId()]);
            }

            if ($userToAdd === $this->getUser()) {
                $this->addFlash(
                    'error',
                    'Vous ne pouvez pas vous ajouter vous-même.'
                );

                return $this->redirectToRoute('group_new-member', ['id' => $group->getId()]);
            }

            $userGroup = (new UserGroup())
                ->setUser($userToAdd)
                ->setUserGroup($group)
                ->setAcceptedTheInvitation(false)
            ;

            if ($form->getData()['roleInGroup']) {
                $userGroup->setRoleInGroup(RoleGroup::MODERATOR);
            } else {
                $userGroup->setRoleInGroup(RoleGroup::MEMBER);
            }

            $this->addFlash(
                'success',
                'Invitation envoyée avec succès.'
            );

            $entityManager->persist($userGroup);
            $entityManager->flush();

            return $this->redirectToRoute('group_one', ['id' => $group->getId()]);
        }

        return $this->render('frontend/group/add-member.html.twig', [
            'group' => $group,
            'form' => $form->createView(),
        ]);
    }

    #[Route('invitation/{decision}/{id<\d+>}', name: 'answer-invitation')]
    public function acceptOrDeclineAnInvitation($decision, UserGroup $userGroup, EntityManagerInterface $entityManager): Response
    {
        if ($this->getUser() === $userGroup->getUser()) {
            if ('decline' === $decision) {
                $entityManager->remove($userGroup);

                $this->addFlash(
                    'success',
                    'Invitation déclinée avec succès',
                );
            } else {
                $userGroup->setAcceptedTheInvitation(true);
                $entityManager->persist($userGroup);

                $this->addFlash(
                    'success',
                    'Invitation acceptée avec succès',
                );
            }
            $entityManager->flush();
        }

        return $this->redirectToRoute('group_index');
    }

    #[Route('/{id<\d+>}/rejoindre', name: 'join')]
    public function joinAGroup(Group $group, UserGroupRepository $userGroupRepository, EntityManagerInterface $entityManager): Response
    {
        if ($group->isApproved() && $group->isActive() && 1 === $group->getVisibility()->value) {
            $userGroup = $userGroupRepository->findOneBy(['user' => $this->getUser(), 'userGroup' => $group]);

            if (null === $userGroup) {
                $userGroup = (new UserGroup())
                    ->setUser($this->getUser())
                    ->setUserGroup($group)
                    ->setRoleInGroup(RoleGroup::MEMBER)
                    ->setAcceptedTheInvitation(true)
                ;

                $entityManager->persist($userGroup);
                $entityManager->flush();

                $this->addFlash(
                    'success',
                    'Vous avez rejoint le groupe avec succès',
                );
            } else {
                $this->addFlash(
                    'error',
                    'Vous appartenez déjà à ce groupe.',
                );
            }
        }

        return $this->redirectToRoute('group_one', ['id' => $group->getId()]);
    }

    /**
     * leave a group.
     *
     * @param Group                  $group
     * @param UserGroupRepository    $userGroupRepository
     * @param EntityManagerInterface $entityManager
     *
     * @return Response - group_index
     */
    #[Route('/{id<\d+>}/quitter', name: 'leave')]
    public function leaveAGroup(Group $group, UserGroupRepository $userGroupRepository, EntityManagerInterface $entityManager): Response
    {
        $userGroup = $userGroupRepository->findOneBy(['userGroup' => $group, 'user' => $this->getUser()]);

        if (null !== $userGroup && 1 !== $userGroup->getRoleInGroup()->value) {
            $entityManager->remove($userGroup);
            $entityManager->flush();

            $this->addFlash(
                'success',
                'Groupe quitté avec succès',
            );
        }

        return $this->redirectToRoute('group_index');
    }

    #[Route('/{id<\d+>}/membres', name: 'members')]
    public function indexMembers(Group $group, UserGroupRepository $userGroupRepository, CheckAuthorisationGroupService $checkAuthorisationGroupService): Response
    {
        $connectedUserInGroup = $userGroupRepository->findOneBy(['userGroup' => $group, 'user' => $this->getUser(), 'acceptedTheInvitation' => true]);

        if (false === $checkAuthorisationGroupService->isInStaffAndGroupIsApprovedOrIsAdmin($this->getUser(), $group)) {
            $this->addFlash(
                'error',
                'Vous n\'êtes pas autorisé(e) à accéder à cette page.'
            );

            return $this->redirectToRoute('group_index');
        }

        $members = $userGroupRepository->findBy(['userGroup' => $group, 'acceptedTheInvitation' => true]);

        return $this->render('frontend/group/membersByGroup.html.twig', [
            'members' => $members,
            'group' => $group,
            'connectedUserInGroup' => $connectedUserInGroup,
        ]);
    }

    /**
     * remove a member from a group.
     *
     * @param Group                  $group
     * @param User                   $user
     * @param UserGroupRepository    $userGroupRepository
     * @param EntityManagerInterface $entityManager
     *
     * @return Response - group_members
     */
    #[Route('/{group<\d+>}/membres/{user<\d+>}', name: 'remove')]
    public function removeMember(Group $group, User $user, UserGroupRepository $userGroupRepository, EntityManagerInterface $entityManager): Response
    {
        $userGroup = $userGroupRepository->findOneBy(['user' => $user, 'userGroup' => $group]);
        $connectedUserInGroup = $userGroupRepository->findOneBy(['userGroup' => $group, 'user' => $this->getUser(), 'acceptedTheInvitation' => true]);

        if (null !== $userGroup && null !== $connectedUserInGroup) {
            if ($userGroup->getRoleInGroup()->value > $connectedUserInGroup->getRoleInGroup()->value) {
                $entityManager->remove($userGroup);
                $entityManager->flush();

                $this->addFlash(
                    'success',
                    'Cet utilisateur a été retiré du groupe avec succès.',
                );
            } else {
                $this->addFlash(
                    'error',
                    'Vous n\'avez pas l\'autorisation nécessaire pour retirer cet utilisateur du groupe.'
                );
            }
        } else {
            $this->addFlash(
                'error',
                'Action impossible.'
            );
        }

        return $this->redirectToRoute('group_members', ['id' => $group->getId()]);
    }

    #[Route('/{id<\d+>}/membres/transferer-groupe', name: 'transfer')]
    public function transferGroup(Group $group, Request $request, UserGroupRepository $userGroupRepository, UserRepository $userRepository, EntityManagerInterface $entityManager): Response
    {
        $connectedUserInGroup = $userGroupRepository->findOneBy(['userGroup' => $group, 'user' => $this->getUser(), 'acceptedTheInvitation' => true]);

        if (!$this->isGranted('ROLE_ADMIN')) {
            if (null === $connectedUserInGroup || 1 !== $connectedUserInGroup->getRoleInGroup()->value) {
                $this->addFlash(
                    'error',
                    'Vous n\'avez pas accès à cette page.'
                );

                return $this->redirectToRoute('group_index');
            }
        }

        $form = $this->createForm(TransferGroup::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $userToTransfer = $userRepository->findOneBy(['email' => $form->getData()['user'], 'isApproved' => true]);

            $userGroup = $userGroupRepository->findOneBy(['user' => $userToTransfer, 'userGroup' => $group, 'acceptedTheInvitation' => true]);

            if (null === $userToTransfer || null === $userGroup) {
                $this->addFlash(
                    'error',
                    'Cet utilisateur n\'est pas dans votre groupe. Précisions : veuillez vous assurer d\'indiquer le mail lié au compte de l\'utilisateur. Ce dernier peut être différent de celui affiché dans l\'annuaire.'
                );

                return $this->redirectToRoute('group_index');
            }

            $lastAdmin = $userGroupRepository->findOneBy(['roleInGroup' => 1, 'userGroup' => $group]);

            $lastAdmin->setRoleInGroup(RoleGroup::MODERATOR);
            $userGroup->setRoleInGroup(RoleGroup::ADMIN);

            $entityManager->persist($lastAdmin);
            $entityManager->persist($userGroup);
            $entityManager->flush();

            $this->addFlash(
                'success',
                'Groupe transféré avec succès.'
            );

            return $this->redirectToRoute('group_index');
        }

        return $this->render('frontend/group/transfer-group.html.twig', [
            'group' => $group,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/{id<\d+>}/evenements', name: 'index-events')]
    public function indexEvents(Group $group, CheckAuthorisationGroupService $checkAuthorisationGroupService, EventRepository $eventRepository, Request $request, EntityManagerInterface $entityManager)
    {
        if ($checkAuthorisationGroupService->isInStaffAndGroupIsApproved($this->getUser(), $group) === false) {
            $this->addFlash(
                'error',
                'Vous n\'êtes pas autorisé(e) à accéder à cette page.'
            );

            return $this->redirectToRoute('group_index');
        }

        $events = $eventRepository->findBy(['groupEvent' => $group], ['id' => 'DESC']);

        $event = new Event();

        $form = $this->createForm(EventType::class, $event);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            if ($form->getData()->isAllDay()) {
                $event->setStart($form->getData()->getStartFullday());
                $event->setEnd($form->getData()->getEndFullday());
            }
            if ($form->getData()->getStart() < $form->getData()->getEnd()) {
                $event->setGroupEvent($group);
                $entityManager->persist($event);
                $entityManager->flush();

                $this->addFlash(
                    'success',
                    'Événement enregistré'
                );
            } else {
                $this->addFlash(
                    'error',
                    'Erreur : La date de fin est antérieure à la date de départ'
                );
            }

            return $this->redirectToRoute('group_index-events', ['id' => $group->getId()]);
        }

        return $this->render('frontend/group/index-events.html.twig', [
            'group' => $group,
            'events' => $events,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/{id<\d+>}/evenement/supprimer/{event<\d+>}', name: 'delete-event')]
    public function deleteEvent(Group $group, Event $event, CheckAuthorisationGroupService $checkAuthorisationGroupService, EntityManagerInterface $entityManager)
    {
        if ($checkAuthorisationGroupService->isInStaffAndGroupIsApproved($this->getUser(), $group) === false) {
            $this->addFlash(
                'error',
                'Vous n\'êtes pas autorisé(e) à accéder à cette page.'
            );

            return $this->redirectToRoute('group_index');
        }

        if ($event->getGroupEvent() !== $group) {
            $this->addFlash(
                'error',
                'Cet événement n\'appartient pas à votre groupe.'
            );

            return $this->redirectToRoute('group_index');
        }

        $entityManager->remove($event);
        $entityManager->flush();

        $this->addFlash(
            'success',
            'Événement supprimé avec succès.'
        );

        return $this->redirectToRoute('group_index-events', ['id' => $group->getId()]);
    }

    #[Route('/{id<\d+>}/article', name: 'new-article')]
    public function newGroupeArticle(Group $group, Request $request, EntityManagerInterface $entityManager, CheckAuthorisationGroupService $checkAuthorisationGroupService)
    {
        if ($checkAuthorisationGroupService->isInStaffAndGroupIsApproved($this->getUser(), $group) === false) {
            $this->addFlash(
                'error',
                'Vous n\'êtes pas autorisé(e) à accéder à cette page.'
            );

            return $this->redirectToRoute('group_index');
        }

        $article = new Article();

        $form = $this->createForm(ArticleGroupType::class, $article);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $article->setGroupArticle($group);
            $article->setStatus(StatusArticle::PUBLIE);
            $entityManager->persist($article);
            $entityManager->flush();

            $this->addFlash(
                'success',
                'Article publié avec succès'
            );

            return $this->redirectToRoute('group_one', ['id' => $group->getId()]);
        }

        return $this->render('frontend/group/handle-article.html.twig', [
            'form' => $form->createView(),
            'group' => $group,
        ]);
    }

    /**
     * to edit a group article.
     *
     * @param Group                          $group
     * @param Article                        $article
     * @param Request                        $request
     * @param EntityManagerInterface         $entityManager
     * @param CheckAuthorisationGroupService $checkAuthorisationGroupService
     *
     * @return RedirectResponse|Response
     */
    #[Route('/{id<\d+>}/article/{article<\d+>}/modifier', name: 'edit-article')]
    public function editGroupArticle(Group $group, Article $article, Request $request, EntityManagerInterface $entityManager, CheckAuthorisationGroupService $checkAuthorisationGroupService)
    {
        if ($checkAuthorisationGroupService->isInStaffAndGroupIsApproved($this->getUser(), $group) === false) {
            $this->addFlash(
                'error',
                'Vous n\'êtes pas autorisé(e) à accéder à cette page.'
            );

            return $this->redirectToRoute('group_index');
        }

        $form = $this->createForm(ArticleGroupType::class, $article);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($article);
            $entityManager->flush();

            $this->addFlash(
                'success',
                'Article modifié avec succès'
            );

            return $this->redirectToRoute('group_one', ['id' => $group->getId()]);
        }

        return $this->render('frontend/group/handle-article.html.twig', [
            'form' => $form->createView(),
            'group' => $group,
        ]);
    }

    /**
     * @param Group               $group
     * @param Article             $article
     * @param UserGroupRepository $userGroupRepository
     *
     * @return Response - frontend/group/one-article.html.twig
     */
    #[Route('/{id<\d+>}/article/{article<\d+>}', name: 'one-article')]
    public function oneArticle(Group $group, Article $article, UserGroupRepository $userGroupRepository): Response
    {
        if ($article->getGroupArticle() !== $group) {
            $this->addFlash(
                'error',
                'Cet article n\"appartient pas à à votre groupe'
            );

            return $this->redirectToRoute('group_index');
        }

        $connectedUserInGroup = $userGroupRepository->findOneBy(['user' => $this->getUser(), 'userGroup' => $group, 'acceptedTheInvitation' => true]);

        return $this->render('frontend/group/one-article.html.twig', [
            'group' => $group,
            'connectedUserInGroup' => $connectedUserInGroup,
            'article' => $article,
        ]);
    }

    /**
     * delete an article in relation with a group.
     *
     * @param Group                  $group
     * @param Article                $article
     * @param EntityManagerInterface $entityManager
     * @param UserGroupRepository    $userGroupRepository
     *
     * @return RedirectResponse - group_index
     */
    #[Route('/{id<\d+>}/article/{article<\d+>}/supprimer', name: 'delete-article')]
    public function deleteArticle(Group $group, Article $article, EntityManagerInterface $entityManager, UserGroupRepository $userGroupRepository): RedirectResponse
    {
        $connectedUserInGroup = $userGroupRepository->findOneBy(['user' => $this->getUser(), 'userGroup' => $group, 'acceptedTheInvitation' => true]);

        if ($connectedUserInGroup->getRoleInGroup()->value !== 1) {
            $this->addFlash(
                'error',
                'Vous n\'êtes pas autorisé(e) à supprimer cet article.'
            );

            return $this->redirectToRoute('group_index');
        }

        $entityManager->remove($article);
        $entityManager->flush();

        $this->addFlash(
            'success',
            'Article supprimé avec succès.'
        );

        return $this->redirectToRoute('group_index');
    }

    /**
     * index all articles of one group.
     *
     * @param Group                          $group
     * @param CheckAuthorisationGroupService $checkAuthorisationGroupService
     * @param ArticleRepository              $articleRepository
     * @param PaginatorInterface             $paginator
     * @param int                            $page
     *
     * @return Response - frontend/group/index-articles.html.twig
     */
    #[Route('/{id<\d+>}/articles', name: 'index-articles', defaults: ['page' => 1])]
    public function indexArticles(Group $group, CheckAuthorisationGroupService $checkAuthorisationGroupService, ArticleRepository $articleRepository, PaginatorInterface $paginator, int $page, UserGroupRepository $userGroupRepository): Response
    {
        if (false === $checkAuthorisationGroupService->isUserInTheGroup($this->getUser(), $group)) {
            $this->addFlash(
                'error',
                'Vous devez être membre de ce groupe pour accéder à cette page.'
            );

            return $this->redirectToRoute('group_index');
        }

        $connectedUserInGroup = $userGroupRepository->findOneBy(['user' => $this->getUser(), 'userGroup' => $group, 'acceptedTheInvitation' => true]);

        $articles = $articleRepository->findBy(['groupArticle' => $group], ['publishedAt' => 'DESC']);
        $articles = $paginator->paginate(
            $articles,
            $page,
            12
        );

        return $this->render('frontend/group/index-articles.html.twig', [
            'group' => $group,
            'articles' => $articles,
            'connectedUserInGroup' => $connectedUserInGroup,
        ]);
    }

    /**
     * handle media.
     *
     * @param Group                          $group
     * @param CheckAuthorisationGroupService $checkAuthorisationGroupService
     * @param EntityManagerInterface         $entityManager
     * @param Request                        $request
     * @param MediaGroupRepository           $mediaGroupRepository
     *
     * @return Response - frontend/group/handle-medias.html.twig
     */
    #[Route('/{id<\d+>}/gestion-medias', name: 'handle-media')]
    public function handleMedia(Group $group, CheckAuthorisationGroupService $checkAuthorisationGroupService, EntityManagerInterface $entityManager, Request $request, MediaGroupRepository $mediaGroupRepository): Response
    {
        if ($checkAuthorisationGroupService->isInStaffAndGroupIsApproved($this->getUser(), $group) === false) {
            $this->addFlash(
                'error',
                'Vous n\'êtes pas autorisé(e) à accéder à cette page.'
            );

            return $this->redirectToRoute('group_index');
        }

        $mediaGroup = new MediaGroup();

        $form = $this->createForm(MediaGroupType::class, $mediaGroup);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $mediaGroup->setMediaGroup($group);
            $entityManager->persist($mediaGroup);
            $entityManager->flush();

            $this->addFlash(
                'success',
                'Media publié avec succès'
            );

            return $this->redirectToRoute('group_one', ['id' => $group->getId()]);
        }

        $mediasGroup = $mediaGroupRepository->findBy(['mediaGroup' => $group], ['id' => 'DESC']);

        return $this->render('frontend/group/handle-medias.html.twig', [
            'group' => $group,
            'form' => $form->createView(),
            'medias' => $mediasGroup,
        ]);
    }

    /**
     * delete a media.
     *
     * @param Group                          $group
     * @param MediaGroup                     $mediaGroup
     * @param CheckAuthorisationGroupService $checkAuthorisationGroupService
     * @param EntityManagerInterface         $entityManager
     *
     * @return RedirectResponse - group_handle-media
     */
    #[Route('/{id<\d+>}/media/supprimer/{mediaGroup<\d+>}', name: 'delete-media')]
    public function deleteMedia(Group $group, MediaGroup $mediaGroup, CheckAuthorisationGroupService $checkAuthorisationGroupService, EntityManagerInterface $entityManager): RedirectResponse
    {
        if ($checkAuthorisationGroupService->isInStaffAndGroupIsApproved($this->getUser(), $group) === false) {
            $this->addFlash(
                'error',
                'Vous n\'êtes pas autorisé(e) à accéder à cette page.'
            );

            return $this->redirectToRoute('group_index');
        }

        if ($mediaGroup->getMediaGroup() !== $group) {
            $this->addFlash(
                'error',
                'Cet événement n\'appartient pas à votre groupe.'
            );

            return $this->redirectToRoute('group_index');
        }

        $entityManager->remove($mediaGroup);
        $entityManager->flush();

        $this->addFlash(
            'success',
            'Média supprimé avec succès.'
        );

        return $this->redirectToRoute('group_handle-media', ['id' => $group->getId()]);
    }

    /**
     * index all media of a group.
     *
     * @param Group                          $group
     * @param MediaGroupRepository           $mediaGroupRepository
     * @param CheckAuthorisationGroupService $checkAuthorisationGroupService
     * @param UserGroupRepository            $userGroupRepository
     *
     * @return Response - frontend/group/index-medias.html.twig
     */
    #[Route('/{id<\d+>}/medias', name: 'index-media')]
    public function indexMedia(Group $group, MediaGroupRepository $mediaGroupRepository, CheckAuthorisationGroupService $checkAuthorisationGroupService, UserGroupRepository $userGroupRepository): Response
    {
        if (false === $checkAuthorisationGroupService->isUserInTheGroup($this->getUser(), $group)) {
            $this->addFlash(
                'error',
                'Vous devez être membre de ce groupe pour accéder à cette page.'
            );

            return $this->redirectToRoute('group_index');
        }

        $connectedUserInGroup = $userGroupRepository->findOneBy(['user' => $this->getUser(), 'userGroup' => $group, 'acceptedTheInvitation' => true]);

        $medias = $mediaGroupRepository->findBy(['mediaGroup' => $group], ['id' => 'DESC']);

        return $this->render('frontend/group/index-medias.html.twig', [
            'group' => $group,
            'medias' => $medias,
            'connectedUserInGroup' => $connectedUserInGroup,
        ]);
    }

    #[Route('/{id<\d+>}/export-emails', name: 'export-emails')]
    public function exportEmails(Group $group, UserGroupRepository $userGroupRepository, CheckAuthorisationGroupService $checkAuthorisationGroupService)
    {
        if (false === $checkAuthorisationGroupService->isInStaffAndGroupIsApprovedOrIsAdmin($this->getUser(), $group)) {
            $this->addFlash(
                'error',
                'Vous n\'avez pas l\'autorisation d\'effectuer cette action.'
            );

            return $this->redirectToRoute('group_index');
        }

        $usersInTheGroup = $userGroupRepository->findBy(['userGroup' => $group, 'acceptedTheInvitation' => true]);

        $emails = [];
        foreach ($usersInTheGroup as $userGroup) {
            $emails[] = $userGroup->getUser()->getEmail();
        }
        $header_args = ['Emails'];
        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename=emails.csv');
        $output = fopen('php://output', 'w');
        ob_end_clean();
        fputcsv($output, $header_args);
        $data = [
            $emails,
        ];
        foreach ($data as $data_item) {
            fputcsv($output, $data_item);
        }
        exit;
    }
}
