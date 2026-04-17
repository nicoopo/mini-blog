<?php

namespace App\DataFixtures;

use App\Entity\Article;
use App\Entity\Comment;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Bundle\FixturesBundle\FixtureGroupInterface;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class CommentFixtures extends Fixture implements FixtureGroupInterface, DependentFixtureInterface
{
    public static function getGroups(): array
    {
        return ['comment'];
    }

    public function getDependencies(): array
    {
        return [
            UserFixtures::class,
            ArticleFixtures::class,
        ];
    }

    public function load(ObjectManager $manager): void
    {
        $comments = [
            ['Super article, très instructif !',          0, 0, 'approved'],
            ['Je ne suis pas d\'accord avec ce point.',   1, 0, 'approved'],
            ['Merci pour ce contenu de qualité.',         2, 1, 'approved'],
            ['Très bien écrit, bravo !',                  3, 1, 'pending'],
            ['Des sources seraient les bienvenues.',      4, 2, 'approved'],
            ['Article passionnant, j\'en veux plus !',   0, 2, 'approved'],
            ['Un peu trop partial à mon goût.',           1, 3, 'rejected'],
            ['Exactement ce que je cherchais, merci.',   2, 3, 'approved'],
            ['On attend la suite avec impatience.',       3, 4, 'pending'],
            ['Quelques coquilles dans le texte.',         4, 4, 'approved'],
            ['Très bon résumé de la situation.',          0, 5, 'approved'],
            ['Je partage cet article à mes amis.',        1, 5, 'approved'],
            ['Analyse trop simpliste.',                   2, 6, 'rejected'],
            ['Bel article, continue comme ça !',         3, 6, 'approved'],
            ['Intéressant mais incomplet.',               4, 7, 'pending'],
            ['Wow, je ne savais pas ça !',               0, 7, 'approved'],
            ['Excellent point de vue.',                   1, 8, 'approved'],
            ['À partager absolument.',                    2, 8, 'approved'],
            ['Pas convaincu par les arguments.',          3, 9, 'rejected'],
            ['Merci pour ces informations claires.',     4, 9, 'approved'],
        ];

        foreach ($comments as [$content, $userIndex, $articleDays, $status]) {
            $comment = new Comment();
            $comment->setContent($content)
                ->setCreatedAt(new \DateTimeImmutable())
                ->setStatus($status)
                ->setIsApproved($status === 'approved')
                ->setAuthor($this->getReference("user_{$userIndex}", User::class))
                ->setArticle($this->getReference("article_{$articleDays}", Article::class));
            $manager->persist($comment);
        }

        $manager->flush();
    }
}
