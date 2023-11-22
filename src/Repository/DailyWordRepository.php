<?php

namespace App\Repository;

use App\Entity\DailyWord;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<DailyWord>
 *
 * @method DailyWord|null find($id, $lockMode = null, $lockVersion = null)
 * @method DailyWord|null findOneBy(array $criteria, array $orderBy = null)
 * @method DailyWord[]    findAll()
 * @method DailyWord[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class DailyWordRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, DailyWord::class);
    }

//    /**
//     * @return DailyWord[] Returns an array of DailyWord objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('d')
//            ->andWhere('d.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('d.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?DailyWord
//    {
//        return $this->createQueryBuilder('d')
//            ->andWhere('d.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
