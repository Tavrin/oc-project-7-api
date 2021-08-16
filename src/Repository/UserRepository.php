<?php

namespace App\Repository;

use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Security\Core\User\PasswordUpgraderInterface;

/**
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

    public function findClientUsers(int $clientId, int $firstPage = 1, int $limit = null, array $sort = null): Paginator
    {
        if (null === $limit) {
            $limit = 10;
        }

        $offset = ($firstPage * $limit) - $limit;

        $query = $this->createQueryBuilder('u')
            ->orderBy('u.createdAt', 'DESC')
            ->setFirstResult($offset)
            ->join('u.client', 'c')
            ->where('c.id = :clientId')
            ->setParameter('clientId', $clientId)
        ;

        if (isset($limit) && 0 !== $limit) {
            $query->setMaxResults($limit);
        }

        if (isset($sort)) {
            foreach ($sort as $column) {
                if ('-' === $column[0]) {
                    $column = substr($column, 1);
                    $query->addOrderBy('u.'.$column, 'ASC');
                } else {
                    $query->addOrderBy('u.'.$column, 'DESC');
                }
            }
        }

        return new Paginator($query, true);
    }

    // /**
    //  * @return User[] Returns an array of User objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('u')
            ->andWhere('u.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('u.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?User
    {
        return $this->createQueryBuilder('u')
            ->andWhere('u.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
