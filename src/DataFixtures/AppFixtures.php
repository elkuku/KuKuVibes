<?php

namespace App\DataFixtures;

use App\Entity\Category;
use App\Entity\Feed;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class AppFixtures extends Fixture
{
    #[\Override]
    public function load(ObjectManager $manager): void
    {
        $user = (new User())
            ->setIdentifier('user');
        $manager->persist($user);

        $manager->persist(
            (new User())
                ->setIdentifier('admin')
                ->setRoles([User::ROLES['admin']])
        );

        $category = (new Category())
            ->setName('TEST');
        $manager->persist($category);

$manager->persist((new Feed())
            ->setName('TEST')
->setUrl('TEST.com')
->setCategory($category)
->setOwner($user));


        $manager->flush();
    }
}
