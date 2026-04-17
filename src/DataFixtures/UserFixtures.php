<?php

namespace App\DataFixtures;

use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Bundle\FixturesBundle\FixtureGroupInterface;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserFixtures extends Fixture implements FixtureGroupInterface
{
    public function __construct(
        private UserPasswordHasherInterface $hasher
    ) {}

    public static function getGroups(): array
    {
        return ['user'];
    }

    public function load(ObjectManager $manager): void
    {
        // Admin
        $admin = new User();
        $admin->setEmail('admin@blog.fr')
            ->setUsername('admin')
            ->setFirstName('Admin')
            ->setLastName('Super')
            ->setRoles(['ROLE_ADMIN'])
            ->setIsActive(true)
            ->setCreatedAt(new \DateTimeImmutable())
            ->setPassword($this->hasher->hashPassword($admin, 'admin1234'));

        $manager->persist($admin);
        $this->addReference('user_admin', $admin);

        // Users classiques
        $users = [
            ['jean@blog.fr',    'jdupont',  'Jean',    'Dupont',   'password123'],
            ['marie@blog.fr',   'mmartin',  'Marie',   'Martin',   'password123'],
            ['pierre@blog.fr',  'pdurand',  'Pierre',  'Durand',   'password123'],
            ['lucie@blog.fr',   'lbernard', 'Lucie',   'Bernard',  'password123'],
            ['thomas@blog.fr',  'tleroy',   'Thomas',  'Leroy',    'password123'],
        ];

        foreach ($users as $i => [$email, $username, $firstName, $lastName, $pwd]) {
            $user = new User();
            $user->setEmail($email)
                ->setUsername($username)
                ->setFirstName($firstName)
                ->setLastName($lastName)
                ->setRoles(['ROLE_USER'])
                ->setIsActive(true)
                ->setCreatedAt(new \DateTimeImmutable("-{$i} days"))
                ->setPassword($this->hasher->hashPassword($user, $pwd));

            $manager->persist($user);
            $this->addReference("user_{$i}", $user);
        }

        $manager->flush();
    }
}
