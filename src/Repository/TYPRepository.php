<?php

namespace App\Repository;

use App\Entity\TYP;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method TYP|null find($id, $lockMode = null, $lockVersion = null)
 * @method TYP|null findOneBy(array $criteria, array $orderBy = null)
 * @method TYP[]    findAll()
 * @method TYP[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TYPRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, TYP::class);
    }

    // /**
    //  * @return TYP[] Returns an array of TYP objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('t')
            ->andWhere('t.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('t.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?TYP
    {
        return $this->createQueryBuilder('t')
            ->andWhere('t.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
