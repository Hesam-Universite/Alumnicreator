<?php

namespace App\Repository;

use App\Entity\DirectoryPageInstance;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<DirectoryPageInstance>
 *
 * @method DirectoryPageInstance|null find($id, $lockMode = null, $lockVersion = null)
 * @method DirectoryPageInstance|null findOneBy(array $criteria, array $orderBy = null)
 * @method DirectoryPageInstance[]    findAll()
 * @method DirectoryPageInstance[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class DirectoryPageInstanceRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, DirectoryPageInstance::class);
    }

    public function add(DirectoryPageInstance $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(DirectoryPageInstance $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function findByFilters($keyword, $class)
    {
        $query = $this->createQueryBuilder('d');

        if ($keyword !== null) {
            $query->andWhere('d.firstname LIKE :keyword OR d.lastname LIKE :keyword')
                ->setParameter('keyword', '%'.$keyword.'%');
        }

        if ($class !== null) {
            $query->andWhere('d.class = :class')
                ->setParameter('class', $class);
        }

        return $query->orderBy('d.lastname', 'ASC')
            ->getQuery()
            ->getResult()
        ;
    }
}
