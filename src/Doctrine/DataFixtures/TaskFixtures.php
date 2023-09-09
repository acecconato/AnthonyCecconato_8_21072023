<?php

declare(strict_types=1);

namespace App\Doctrine\DataFixtures;

use App\Entity\Task;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;

class TaskFixtures extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create('fr');
        $users = $manager->getRepository(User::class)->findAll();
        foreach ($users as $user) {
            for ($i = 0; $i < 20; ++$i) {
                $task = new Task();
                $task
                    ->setOwner($user)
                    ->setTitle($faker->realTextBetween(10, 255))
                    ->setContent($faker->text(1000))
                    ->setCompleted($i < 10);

                $manager->persist($task);
            }
        }

        for ($i = 0; $i < 20; ++$i) {
            $task = new Task();
            $task
                ->setTitle($faker->realTextBetween(10, 255))
                ->setContent($faker->text(1000))
                ->setCompleted($i < 10);
            $manager->persist($task);
        }

        $manager->flush();
    }

    /**
     * @return list<class-string<FixtureInterface>>
     */
    public function getDependencies(): array
    {
        return [UserFixtures::class];
    }
}
