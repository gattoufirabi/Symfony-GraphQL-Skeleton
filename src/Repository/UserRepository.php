<?php

namespace App\Repository;

use App\Entity\User;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\PasswordUpgraderInterface;

class UserRepository extends GlobalRepository implements PasswordUpgraderInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, User::class);
    }

    /**
     * Used to upgrade (rehash) the user's password automatically over time.
     */

    public function loadUserByUsername(string $email): ?User
    {
        $qb = $this->createQueryBuilder('u');

        $qb->andWhere($qb->expr()->eq('u.email', ':email'))
            ->andWhere($qb->expr()->eq('u.isEnabled', ':enabled'))
            ->setParameter('email', $email)
            ->setParameter('enabled', true);

        return $qb->getQuery()->getOneOrNullResult();
    }
    public function upgradePassword(PasswordAuthenticatedUserInterface $user, string $newHashedPassword): void
    {
        if (!$user instanceof User) {
            throw new UnsupportedUserException(sprintf('Instances of "%s" are not supported.', $user::class));
        }

        $user->setPassword($newHashedPassword);
        $this->getEntityManager()->persist($user);
        $this->getEntityManager()->flush();
    }

    public function getUsers(User $auth, $offset, $limit, $search)
    {
        $qb = $this->createQueryBuilder('user');

        if ($search) {
            $qb
                ->orWhere($qb->expr()->like('UPPER(user.name)', ':name'))
                ->orWhere('MATCH_AGAINST(user.name) AGAINST(:against boolean)>0')
                ->setParameter('against', strtoupper($search))
                ->setParameter('name', '%' . $search . '%');
        }

        $qb->addOrderBy('user.name', 'ASC');
        $qb->groupBy('user.id');

        if ($limit > 0) {
            $qb->setMaxResults($limit);
        }

        return $qb->setFirstResult($offset)
            ->getQuery()// ->setHint(Query::HINT_FORCE_PARTIAL_LOAD, true)
            ->getResult();
    }

    public function countAllByUser(User $auth): ?int
    {
        $qb = $this->createQueryBuilder('user')
            ->select('COUNT(user)');

        if ($search) {
            $qb
                ->orWhere($qb->expr()->like('UPPER(user.firstName)', ':name'))
                ->orWhere($qb->expr()->like('UPPER(user.lastName)', ':name'))
                ->orWhere('MATCH_AGAINST(user.firstName, user.lastName) AGAINST(:against boolean)>0')
                ->setParameter('against', strtoupper($search))
                ->setParameter('name', '%' . $search . '%');
        }

        return $qb->getQuery()->getSingleScalarResult();
    }

    //    /**
    //     * @return User[] Returns an array of User objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('u')
    //            ->andWhere('u.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('u.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?User
    //    {
    //        return $this->createQueryBuilder('u')
    //            ->andWhere('u.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
