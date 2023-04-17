<?php

namespace App\Service;

use App\Entity\Group;
use App\Repository\ArticleRepository;
use App\Repository\EventRepository;
use App\Repository\MediaGroupRepository;
use App\Repository\UserGroupRepository;
use Doctrine\ORM\EntityManagerInterface;

class DeleteGroupService
{
    public function __construct(
        private UserGroupRepository $userGroupRepository,
        private ArticleRepository $articleRepository,
        private EventRepository $eventRepository,
        private MediaGroupRepository $mediaGroupRepository,
        private EntityManagerInterface $entityManager,
    ) {
    }

    public function deleteGroup(Group $group): void
    {
        $usersGroup = $this->userGroupRepository->findBy(['userGroup' => $group]);
        $articlesToDelete = $this->articleRepository->findBy(['groupArticle' => $group]);
        $eventsToDelete = $this->eventRepository->findBy(['groupEvent' => $group]);
        $mediasToDelete = $this->mediaGroupRepository->findBy(['mediaGroup' => $group]);

        foreach ($usersGroup as $userGroup) {
            $this->entityManager->remove($userGroup);
        }

        foreach ($articlesToDelete as $article) {
            $this->entityManager->remove($article);
        }

        foreach ($eventsToDelete as $event) {
            $this->entityManager->remove($event);
        }

        foreach ($mediasToDelete as $media) {
            $this->entityManager->remove($media);
        }

        $this->entityManager->remove($group);
        $this->entityManager->flush();
    }
}
