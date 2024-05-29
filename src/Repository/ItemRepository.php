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

    public function search($query) {
        return $this->createQueryBuilder('i')
            ->add('where', 'MATCH_AGAINST(i.name, :query) > 0')
            ->setParameter('query', $query)
            ->setMaxResults(10)
            ->getQuery()
            ->getResult();
    }

    public function FindOneByIdJoined(int $itemId) {
        $em = $this->getEntityManager();

        $query = $em->createQuery(
            '
            SELECT i, ic, t, at
            FROM App\Entity\Item i
            LEFT JOIN i.itemCollection ic
            LEFT JOIN i.tags t
            LEFT JOIN i.itemAttributes at
            WHERE i.id = :id
            '
        )->setParameter('id', $itemId);

        return $query->getOneOrNullResult();
    }

    public function findLatestItems() {
        return $this->createQueryBuilder('i')
            ->orderBy('i.createdAt', 'DESC')
            ->setMaxResults(6)
            ->getQuery()
            ->getResult();
    }
}
