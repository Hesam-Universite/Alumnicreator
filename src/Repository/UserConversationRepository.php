<?php

namespace App\Repository;

use App\Entity\UserConversation;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<UserConversation>
 *
 * @method UserConversation|null find($id, $lockMode = null, $lockVersion = null)
 * @method UserConversation|null findOneBy(array $criteria, array $orderBy = null)
 * @method UserConversation[]    findAll()
 * @method UserConversation[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class UserConversationRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, UserConversation::class);
    }

    public function add(UserConversation $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(UserConversation $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    /**
     * @throws NonUniqueResultException
     */
    public function findOneByIdAndUserIsNotUserLogged($conversation, $user)
    {
        return $this->createQueryBuilder('u')
            ->andWhere('u.conversation = :conversation AND u.user != :user')
            ->setParameter('conversation', $conversation)
            ->setParameter('user', $user)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }

    /**
     * @throws NonUniqueResultException
     */
    public function countByConversation($conversation)
    {
        return $this->createQueryBuilder('u')
            ->select('count(u.id)')
            ->andWhere('u.conversation = :conversation')
            ->setParameter('conversation', $conversation)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
}
