<?php

namespace App\Repository;

use App\Entity\ResumeInstance;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<ResumeInstance>
 *
 * @method ResumeInstance|null find($id, $lockMode = null, $lockVersion = null)
 * @method ResumeInstance|null findOneBy(array $criteria, array $orderBy = null)
 * @method ResumeInstance[]    findAll()
 * @method ResumeInstance[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ResumeInstanceRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ResumeInstance::class);
    }

    public function add(ResumeInstance $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(ResumeInstance $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function findByFilters($keywords, $activityArea, $status, $department, $region): array
    {
        if (count($activityArea) === 0) {
            $query = $this->createQueryBuilder('r');

            if ($keywords !== null) {
                $query->andWhere('r.presentation LIKE :keywords')
                    ->setParameter('keywords', '%'.$keywords.'%');
            }

            if ($status !== null) {
                $query->andWhere('r.status = :status')
                    ->setParameter('status', $status->value);
            }

            if ($department !== null) {
                $department = $department->value;
                if ($department < 10) {
                    $department = '0'.$department; // I add a '0' where there is only one number in the department, because I check the two firsts characters in the postalCode (ex 01000 => 01)
                }
                $query->andWhere('r.userPostalCode LIKE :department')
                    ->setParameter('department', $department.'%');
            }

            if ($region !== null) {
                $region = $region->value;

                $auvergneRhoneAlpes = ['01', '03', '07', '15', '26', '38', '42', '43', '63', '69', '73', '74'];
                $bourgogneFranceComte = ['21', '25', '39', '58', '70', '71', '89', '90'];
                $bretagne = ['22', '29', '35', '56'];
                $centreValDeLoire = ['18', '28', '36', '37', '41', '45'];
                $corse = ['20'];
                $grandEst = ['08', '10', '51', '52', '54', '55', '57', '67', '68', '88'];
                $hautDeFrance = ['02', '59', '60', '62', '80'];
                $ileDeFrance = ['75', '77', '78', '91', '92', '93', '94', '95'];
                $normandie = ['14', '27', '50', '61', '76'];
                $nouvelleAquitaine = ['16', '17', '19', '23', '24', '33', '40', '47', '64', '79', '86', '87'];
                $occitanie = ['09', '11', '12', '30', '31', '32', '34', '46', '48', '65', '66', '81', '82'];
                $paysDeLaLoire = ['44', '49', '53', '72', '85'];
                $provenceAlpesCotedAzur = ['04', '05', '06', '13', '83', '84'];

                $query->andWhere($query->expr()->substring('r.userPostalCode', 1, 2).'IN (:departements)');

                match ($region) {
                    1 => $query->setParameter('departements', $auvergneRhoneAlpes),
                    2 => $query->setParameter('departements', $bourgogneFranceComte),
                    3 => $query->setParameter('departements', $bretagne),
                    4 => $query->setParameter('departements', $centreValDeLoire),
                    5 => $query->setParameter('departements', $corse),
                    6 => $query->setParameter('departements', $grandEst),
                    7 => $query->setParameter('departements', $hautDeFrance),
                    8 => $query->setParameter('departements', $ileDeFrance),
                    9 => $query->setParameter('departements', $normandie),
                    10 => $query->setParameter('departements', $nouvelleAquitaine),
                    11 => $query->setParameter('departements', $occitanie),
                    12 => $query->setParameter('departements', $paysDeLaLoire),
                    13 => $query->setParameter('departements', $provenceAlpesCotedAzur),
                };
            }

            return $query->orderBy('r.id', 'ASC')
                ->getQuery()
                ->getResult()
            ;
        } else {
            return [];
        }
    }
}
