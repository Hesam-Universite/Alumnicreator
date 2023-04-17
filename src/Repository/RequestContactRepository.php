<?php

namespace App\Repository;

use App\Entity\RequestContact;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<RequestContact>
 *
 * @method RequestContact|null find($id, $lockMode = null, $lockVersion = null)
 * @method RequestContact|null findOneBy(array $criteria, array $orderBy = null)
 * @method RequestContact[]    findAll()
 * @method RequestContact[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class RequestContactRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, RequestContact::class);
    }

    public function add(RequestContact $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(RequestContact $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }
}
