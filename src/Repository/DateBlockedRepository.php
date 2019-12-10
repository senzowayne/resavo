<?php

namespace App\Repository;

use App\Entity\DateBlocked;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method DateBlocked|null find($id, $lockMode = null, $lockVersion = null)
 * @method DateBlocked|null findOneBy(array $criteria, array $orderBy = null)
 * @method DateBlocked[]    findAll()
 * @method DateBlocked[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class DateBlockedRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, DateBlocked::class);
    }

     /**
      * @return DateBlocked[] Returns an array of DateBlocked objects
      */

    public function myfindAll()
    {
        return $this->createQueryBuilder('d')
            ->addSelect('d')
            ->getQuery()
            ->getArrayResult();
    }


    /*
    public function findOneBySomeField($value): ?DateBlocked
    {
        return $this->createQueryBuilder('d')
            ->andWhere('d.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
