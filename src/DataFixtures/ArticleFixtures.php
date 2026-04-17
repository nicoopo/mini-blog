<?php

namespace App\DataFixtures;

use App\Entity\Article;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Bundle\FixturesBundle\FixtureGroupInterface;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use App\Entity\User;
use App\Entity\Category;

class ArticleFixtures extends Fixture implements FixtureGroupInterface, DependentFixtureInterface
{
    public static function getGroups(): array
    {
        return ['article'];
    }

    public function getDependencies(): array
    {
        return [
            UserFixtures::class,
            CategoryFixtures::class,
        ];
    }

    public function load(ObjectManager $manager): void
    {
        $articles = [
            [
                'title'   => 'L\'avenir de l\'intelligence artificielle',
                'content' => 'L\'intelligence artificielle transforme notre quotidien à une vitesse sans précédent. Des assistants vocaux aux voitures autonomes, les applications sont infinies et soulèvent des questions éthiques majeures.',
                'picture' => 'ai_future.jpg',
                'category' => 0,
                'author'   => 0,
                'days'     => 10,
            ],
            [
                'title'   => 'Les Jeux Olympiques 2024 : bilan',
                'content' => 'Paris a accueilli les Jeux Olympiques avec brio. Retour sur les performances des athlètes français et les moments marquants de cette édition historique.',
                'picture' => 'jo2024.jpg',
                'category' => 1,
                'author'   => 1,
                'days'     => 8,
            ],
            [
                'title'   => 'Top 10 des films de l\'année',
                'content' => 'Entre blockbusters et films indépendants, cette année cinématographique a offert des œuvres mémorables. Découvrez notre sélection des 10 meilleurs films.',
                'picture' => 'cinema.jpg',
                'category' => 2,
                'author'   => 2,
                'days'     => 7,
            ],
            [
                'title'   => 'La conquête de Mars : où en est-on ?',
                'content' => 'SpaceX, NASA, et d\'autres acteurs privés accélèrent leurs programmes. L\'humanité se rapproche d\'une présence permanente sur la planète rouge.',
                'picture' => 'mars.jpg',
                'category' => 3,
                'author'   => 3,
                'days'     => 6,
            ],
            [
                'title'   => 'Élections : les enjeux pour 2025',
                'content' => 'À l\'approche des prochaines échéances électorales, les partis repositionnent leurs programmes. Analyse des grandes tendances qui vont façonner le paysage politique.',
                'picture' => 'elections.jpg',
                'category' => 4,
                'author'   => 4,
                'days'     => 5,
            ],
            [
                'title'   => 'ChatGPT et le monde du travail',
                'content' => 'L\'IA générative bouleverse les métiers. Quels sont les secteurs les plus impactés et comment les entreprises s\'adaptent-elles à cette révolution ?',
                'picture' => 'chatgpt.jpg',
                'category' => 0,
                'author'   => 0,
                'days'     => 4,
            ],
            [
                'title'   => 'Football : le mercato de l\'été',
                'content' => 'Un été agité pour les clubs européens avec des transferts records. Analyse des meilleurs coups réalisés et des paris risqués de la saison.',
                'picture' => 'football.jpg',
                'category' => 1,
                'author'   => 1,
                'days'     => 3,
            ],
            [
                'title'   => 'La musique streaming : état des lieux',
                'content' => 'Spotify, Deezer, Apple Music... Le streaming domine l\'industrie musicale. Quelles sont les conséquences pour les artistes et les labels ?',
                'picture' => 'music.jpg',
                'category' => 2,
                'author'   => 2,
                'days'     => 2,
            ],
            [
                'title'   => 'Le changement climatique en chiffres',
                'content' => 'Les derniers rapports du GIEC dressent un tableau alarmant. Températures, montée des eaux, biodiversité : les données qui doivent nous alerter.',
                'picture' => 'climate.jpg',
                'category' => 3,
                'author'   => 3,
                'days'     => 1,
            ],
            [
                'title'   => 'Budget 2025 : ce qui change pour vous',
                'content' => 'Le gouvernement présente son projet de loi de finances. Impôts, aides sociales, investissements publics : décryptage des mesures qui vont impacter les Français.',
                'picture' => 'budget.jpg',
                'category' => 4,
                'author'   => 4,
                'days'     => 0,
            ],
        ];

        foreach ($articles as $index => $data) {
            $article = new Article();
            $article->setTitle($data['title'])
                ->setContent($data['content'])
                ->setPicture($data['picture'])
                ->setCreatedAt(new \DateTimeImmutable("-{$data['days']} days"))
                ->setPublishedAt(new \DateTimeImmutable("-{$data['days']} days"))
                ->setAuthor($this->getReference("user_{$data['author']}", User::class))
                ->setCategory($this->getReference("category_{$data['category']}", Category::class));


            $manager->persist($article);
            $this->addReference("article_{$index}", $article);        }

        $manager->flush();
    }
}
