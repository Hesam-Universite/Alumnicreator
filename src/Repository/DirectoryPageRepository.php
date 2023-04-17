<?php

namespace App\Repository;

use App\Entity\DirectoryPage;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<DirectoryPage>
 *
 * @method DirectoryPage|null find($id, $lockMode = null, $lockVersion = null)
 * @method DirectoryPage|null findOneBy(array $criteria, array $orderBy = null)
 * @method DirectoryPage[]    findAll()
 * @method DirectoryPage[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class DirectoryPageRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, DirectoryPage::class);
    }

    public function add(DirectoryPage $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(DirectoryPage $entity, bool $flush = false): void
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

    public function findByToken(string $token)
    {
        return $this->createQueryBuilder('d')
            ->andWhere('MD5(d.id) = :token')
                ->setParameter('token', $token)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
}
