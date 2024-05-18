<?php

namespace App\Repository;

use App\Entity\Item;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Item>
 */
class ItemRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Item::class);
    }

    //   public function search(string $query)
    // {
    //     return $this->createQueryBuilder('i')
    //         ->where('MATCH (name) AGAINST (:query IN BOOLEAN MODE) > 0')
    //         ->setParameter('query', $query)
    //         ->getQuery()
    //         ->getResult();
    // }

    public function search($query) {
        return $this->createQueryBuilder('i')
            // ->addSelect("MATCH_AGAINST (i.name, :query 'IN NATURAL MODE') as score")
            ->add('where', 'MATCH_AGAINST(i.name, :query) > 0')
            ->setParameter('query', $query)
            // ->orderBy('score', 'desc')
            // ->setMaxResults(5)
            ->getQuery()
            ->getResult();
    }


    //    /**
    //     * @return Item[] Returns an array of Item objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('i')
    //            ->andWhere('i.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('i.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?Item
    //    {
    //        return $this->createQueryBuilder('i')
    //            ->andWhere('i.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
