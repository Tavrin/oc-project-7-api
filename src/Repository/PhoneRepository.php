<?php

namespace App\Repository;

use App\Entity\Phone;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Phone|null find($id, $lockMode = null, $lockVersion = null)
 * @method Phone|null findOneBy(array $criteria, array $orderBy = null)
 * @method Phone[]    findAll()
 * @method Phone[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PhoneRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Phone::class);
    }

    public function findPaginatedPhones(int $firstPage = 1, int $limit = null, array $sort = null): Paginator
    {
        if (null === $limit) {
            $limit = 10;
        }

        $offset = ($firstPage * $limit) - $limit;
        $query = $this->createQueryBuilder('p')
            ->orderBy('p.createdAt', 'DESC')
            ->setFirstResult($offset)
        ;

        if (isset($limit) && 0 !== $limit) {
            $query->setMaxResults($limit);
        }

        if (isset($sort)) {
            foreach ($sort as $column) {
                if ('-' === $column[0]) {
                    $column = substr($column, 1);
                    $query->addOrderBy('p.'.$column, 'ASC');
                } else {
                    $query->addOrderBy('p.'.$column, 'DESC');
                }
            }
        }

        return new Paginator($query, true);
    }

    // /**
    //  * @return Phone[] Returns an array of Phone objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('p.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Phone
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
