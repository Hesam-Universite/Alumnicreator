<?php

namespace App\Repository;

use App\Entity\UserInstance;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<UserInstance>
 *
 * @method UserInstance|null find($id, $lockMode = null, $lockVersion = null)
 * @method UserInstance|null findOneBy(array $criteria, array $orderBy = null)
 * @method UserInstance[]    findAll()
 * @method UserInstance[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class UserInstanceRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, UserInstance::class);
    }

    public function add(UserInstance $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(UserInstance $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function findByRole($role): array
    {
        return $this->createQueryBuilder('u')
            ->andWhere('u.roles LIKE :role')
            ->setParameter('role', '%"'.$role.'"%')
            ->orderBy('u.id', 'ASC')
            ->getQuery()
            ->getResult()
        ;
    }
}
