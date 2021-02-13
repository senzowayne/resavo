<?php
/**
 * Created by PhpStorm.
 * User: senzowayne
 * Date: 06/03/2019
 * Time: 04:33
 */

namespace App\Repository;


use App\Entity\Paypal;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Paypal|null find($id, $lockMode = null, $lockVersion = null)
 * @method Paypal|null findOneBy(array $criteria, array $orderBy = null)
 * @method Paypal[]    findAll()
 * @method Paypal[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PaypalRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Paypal::class);
    }

}
