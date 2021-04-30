<?php

namespace App\Repository;

use App\Entity\Statsms;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method Statsms|null find($id, $lockMode = null, $lockVersion = null)
 * @method Statsms|null findOneBy(array $criteria, array $orderBy = null)
 * @method Statsms[]    findAll()
 * @method Statsms[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class StatsmsRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Statsms::class);
    }

    // /**
    //  * @return Statsms[] Returns an array of Statsms objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('s')
            ->andWhere('s.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('s.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Statsms
    {
        return $this->createQueryBuilder('s')
            ->andWhere('s.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
