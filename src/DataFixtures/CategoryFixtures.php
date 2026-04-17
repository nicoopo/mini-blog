<?php

namespace App\DataFixtures;

use App\Entity\Category;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Bundle\FixturesBundle\FixtureGroupInterface;
use Doctrine\Persistence\ObjectManager;

class CategoryFixtures extends Fixture implements FixtureGroupInterface
{
    public static function getGroups(): array
    {
        return ['category'];
    }

    public function load(ObjectManager $manager): void
    {
        $categories = [
            ['Technologie',  'Articles sur la tech et l\'innovation'],
            ['Sport',        'Actualités sportives et analyses'],
            ['Culture',      'Art, cinéma, musique et littérature'],
            ['Science',      'Découvertes scientifiques et recherches'],
            ['Politique',    'Actualités politiques nationales et internationales'],
        ];

        foreach ($categories as $i => [$name, $description]) {
            $category = new Category();
            $category->setName($name)
                ->setDescription($description);

            $manager->persist($category);
            $this->addReference("category_{$i}", $category);
        }

        $manager->flush();
    }
}
