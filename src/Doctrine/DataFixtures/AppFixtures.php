<?php

namespace App\Doctrine\DataFixtures;

use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AppFixtures extends Fixture
{
    public function __construct(
        private readonly UserPasswordHasherInterface $hasher
    ) {
    }

    public function load(ObjectManager $manager): void
    {
        $user = new User();
        $user
            ->setEmail('demo@demo.fr')
            ->setUsername('demo')
            ->setPassword($this->hasher->hashPassword($user, 'demo'));

        $manager->persist($user);

        $manager->flush();
    }
}
