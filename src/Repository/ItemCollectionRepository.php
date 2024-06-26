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

    public function FindOneByIdJoined(int $collectionId) {
        $em = $this->getEntityManager();

        $query = $em->createQuery(
            '
            SELECT ic, ct, at
            FROM App\Entity\ItemCollection ic
            LEFT JOIN ic.category ct
            LEFT JOIN ic.customItemAttributes at
            WHERE ic.id = :id
            '
        )->setParameter('id', $collectionId);

        return $query->getOneOrNullResult();
    }

    public function findLargestCollections() {

        $conn = $this->getEntityManager()->getConnection();

        $sql = '
        SELECT ic.*
        FROM item_collection ic
        LEFT JOIN item i ON ic.id = i.item_collection_id
        GROUP BY 1
        ORDER BY count(i.id) DESC
        LIMIT 5;
        ';

        $resultSet = $conn->executeQuery($sql);
        return $resultSet->fetchAllAssociative();

    }
}
