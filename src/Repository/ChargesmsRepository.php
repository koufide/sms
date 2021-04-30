<?php

namespace App\Repository;

use App\Entity\Chargesms;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method Chargesms|null find($id, $lockMode = null, $lockVersion = null)
 * @method Chargesms|null findOneBy(array $criteria, array $orderBy = null)
 * @method Chargesms[]    findAll()
 * @method Chargesms[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ChargesmsRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Chargesms::class);
    }

    // /**
    //  * @return Chargesms[] Returns an array of Chargesms objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('c.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Chargesms
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
