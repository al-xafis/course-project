<?php

namespace App\DataFixtures;

use App\Entity\CollectionCategory;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AppFixtures extends Fixture
{
    public function __construct(private UserPasswordHasherInterface $passwordHasher) {
    }

    public function load(ObjectManager $manager): void
    {
        $books = new CollectionCategory();
        $books->setName('Books');
        $coins = new CollectionCategory();
        $coins->setName('Coins');
        $arts = new CollectionCategory();
        $arts->setName('Arts');
        $other = new CollectionCategory();
        $other->setName('Other');

        $manager->persist($books);
        $manager->persist($coins);
        $manager->persist($arts);
        $manager->persist($other);

        $admin = new User();
        $admin->setEmail('admin@mail.ru');
        $admin->setStatus('Active');
        $admin->setFirstName('Admin');
        $admin->setLastName('Admin');
        $admin->setRoles(['ROLE_USER', 'ROLE_ADMIN']);
        $password = 1;
        $hashedPassword = $this->passwordHasher->hashPassword($admin, $password);
        $admin->setPassword($hashedPassword);

        $manager->persist($admin);

        $manager->flush();
    }
}
