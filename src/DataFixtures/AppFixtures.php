<?php

namespace App\DataFixtures;

use App\Entity\CollectionCategory;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $books = new CollectionCategory();
        $books->setName('books');
        $coins = new CollectionCategory();
        $coins->setName('coins');
        $arts = new CollectionCategory();
        $arts->setName('arts');

        $manager->persist($books);
        $manager->persist($coins);
        $manager->persist($arts);

        $manager->flush();
    }
}
