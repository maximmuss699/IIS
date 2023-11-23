<?php

namespace App\Repository;

use App\Entity\KPI;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<KPI>
 *
 * @method KPI|null find($id, $lockMode = null, $lockVersion = null)
 * @method KPI|null findOneBy(array $criteria, array $orderBy = null)
 * @method KPI[]    findAll()
 * @method KPI[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class KPIRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, KPI::class);
    }

//    /**
//     * @return KPI[] Returns an array of KPI objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('k')
//            ->andWhere('k.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('k.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?KPI
//    {
//        return $this->createQueryBuilder('k')
//            ->andWhere('k.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
