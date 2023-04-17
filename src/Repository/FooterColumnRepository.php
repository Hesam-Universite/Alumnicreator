<?php

namespace App\Repository;

use App\Entity\FooterColumn;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<FooterColumn>
 *
 * @method FooterColumn|null find($id, $lockMode = null, $lockVersion = null)
 * @method FooterColumn|null findOneBy(array $criteria, array $orderBy = null)
 * @method FooterColumn[]    findAll()
 * @method FooterColumn[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class FooterColumnRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, FooterColumn::class);
    }

    public function add(FooterColumn $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(FooterColumn $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }
}
