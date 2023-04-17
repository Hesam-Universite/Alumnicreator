<?php

namespace App\Repository;

use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\PasswordUpgraderInterface;

/**
 * @extends ServiceEntityRepository<User>
 *
 * @method User|null find($id, $lockMode = null, $lockVersion = null)
 * @method User|null findOneBy(array $criteria, array $orderBy = null)
 * @method User[]    findAll()
 * @method User[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class UserRepository extends ServiceEntityRepository implements PasswordUpgraderInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, User::class);
    }

    public function add(User $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(User $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    /**
     * Used to upgrade (rehash) the user's password automatically over time.
     */
    public function upgradePassword(PasswordAuthenticatedUserInterface $user, string $newHashedPassword): void
    {
        if (!$user instanceof User) {
            throw new UnsupportedUserException(sprintf('Instances of "%s" are not supported.', \get_class($user)));
        }

        $user->setPassword($newHashedPassword);

        $this->add($user, true);
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

    public function findByRoleAndApproved($role): array
    {
        return $this->createQueryBuilder('u')
            ->andWhere('u.roles LIKE :role')
            ->setParameter('role', '%'.$role.'%')
            ->andWhere('u.isApproved LIKE :isApproved')
            ->setParameter('isApproved', true)
            ->orderBy('u.id', 'ASC')
            ->getQuery()
            ->getResult()
        ;
    }

    public function findAllIdByRoleAndApproved($role): array
    {
        return $this->createQueryBuilder('u')
            ->select('u.id')
            ->andWhere('u.roles LIKE :role')
            ->setParameter('role', '%'.$role.'%')
            ->andWhere('u.isApproved LIKE :isApproved')
            ->setParameter('isApproved', true)
            ->orderBy('u.id', 'ASC')
            ->getQuery()
            ->getResult()
        ;
    }

    public function findCompaniesIdWithActivityArea($activityArea): array
    {
        return $this->createQueryBuilder('u')
            ->select('u.id')
            ->andWhere('u.activityArea = :activityArea')
            ->setParameter('activityArea', $activityArea)
            ->andWhere('u.roles LIKE :role')
            ->setParameter('role', '%"ROLE_COMPANY"%')
            ->getQuery()
            ->getResult()
        ;
    }

    public function findWhereLastConnectionIs($daysAgo): array
    {
        $date = date('Y-m-d', strtotime('-'.$daysAgo.'days'));

        return $this->createQueryBuilder('u')
            ->select('u.id')
            ->andWhere('u.roles LIKE :student or u.roles LIKE :company')
            ->setParameter('student', '%ROLE_STUDENT%')
            ->setParameter('company', '%ROLE_COMPANY%')
            ->andWhere('u.lastConnection = :threeYearsAgo')
            ->setParameter('threeYearsAgo', $date)
            ->getQuery()
            ->getResult()
        ;
    }

    public function findWhereLastConnectionIsMoreThan($daysAgo): array
    {
        $date = date('Y-m-d', strtotime('-'.$daysAgo.'days'));

        return $this->createQueryBuilder('u')
            ->select('u.id')
            ->andWhere('u.roles LIKE :student or u.roles LIKE :company')
            ->setParameter('student', '%ROLE_STUDENT%')
            ->setParameter('company', '%ROLE_COMPANY%')
            ->andWhere('u.lastConnection < :threeYearsAgo')
            ->setParameter('threeYearsAgo', $date)
            ->getQuery()
            ->getResult()
        ;
    }
}
