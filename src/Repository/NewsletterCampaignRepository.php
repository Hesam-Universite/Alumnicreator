<?php

namespace App\Repository;

use App\Entity\NewsletterCampaign;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<NewsletterCampaign>
 *
 * @method NewsletterCampaign|null find($id, $lockMode = null, $lockVersion = null)
 * @method NewsletterCampaign|null findOneBy(array $criteria, array $orderBy = null)
 * @method NewsletterCampaign[]    findAll()
 * @method NewsletterCampaign[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class NewsletterCampaignRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, NewsletterCampaign::class);
    }

    public function add(NewsletterCampaign $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(NewsletterCampaign $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    /**
     * @return NewsletterCampaign[] Returns an array of NewsletterCampaign objects
     */
    public function findNewslettersToSend(): array
    {
        $now = new \DateTime();

        return $this->createQueryBuilder('n')
            ->andWhere('n.isSent = 0')
            ->andWhere('n.sendingTime <= :currentDate')
            ->setParameter('currentDate', $now->format('Y-m-d H:i:s'))
            ->getQuery()
            ->getResult()
        ;
    }
}
