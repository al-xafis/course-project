<?php

namespace App\Repository;

use App\Entity\ItemCollection;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<ItemCollection>
 */
class ItemCollectionRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ItemCollection::class);
    }

    public function search($query) {
        return $this->createQueryBuilder('c')
            ->add('where', 'MATCH_AGAINST(c.name, :query) > 0')
            ->setParameter('query', $query)
            ->getQuery()
            ->getResult();
    }

    public function findLargestCollections() {

        $conn = $this->getEntityManager()->getConnection();

        $sql = '
        SELECT ic.*
        FROM item_collection ic
        INNER JOIN item i ON ic.id = i.item_collection_id
        GROUP BY 1
        ORDER BY count(i.id) DESC
        LIMIT 5;
        ';

        $resultSet = $conn->executeQuery($sql);
        return $resultSet->fetchAllAssociative();

    }

    //    /**
    //     * @return ItemCollection[] Returns an array of ItemCollection objects
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

    //    public function findOneBySomeField($value): ?ItemCollection
    //    {
    //        return $this->createQueryBuilder('i')
    //            ->andWhere('i.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
