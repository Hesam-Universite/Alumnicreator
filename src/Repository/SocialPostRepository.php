<?php

namespace App\Repository;

use App\Entity\NewsletterCampaign;
use App\Entity\SocialPost;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<SocialPost>
 *
 * @method SocialPost|null find($id, $lockMode = null, $lockVersion = null)
 * @method SocialPost|null findOneBy(array $criteria, array $orderBy = null)
 * @method SocialPost[]    findAll()
 * @method SocialPost[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class SocialPostRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, SocialPost::class);
    }

    public function add(SocialPost $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(SocialPost $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    /**
     * @return SocialPost[] Returns an array of SocialPost objects
     */
    public function findSocialPostsToSend(): array
    {
        $now = new \DateTime();

        return $this->createQueryBuilder('s')
            ->andWhere('s.isSent = 0')
            ->andWhere('s.timeToPublished <= :currentDate')
            ->setParameter('currentDate', $now->format("Y-m-d H:i:s"))
            ->getQuery()
            ->getResult()
            ;
    }
}
