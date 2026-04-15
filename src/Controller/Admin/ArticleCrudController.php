<?php

namespace App\Controller\Admin;

use App\Entity\Article;
use Doctrine\ORM\EntityManagerInterface;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextareaField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

class ArticleCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Article::class;
    }

    public function createEntity(string $entityFqcn): Article
    {
        $article = new Article();
        $article->setCreatedAt(new \DateTimeImmutable());
        $article->setAuthor($this->getUser());
        return $article;
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            TextField::new('title', 'Titre'),
            TextareaField::new('content', 'Contenu')->hideOnIndex(),
            AssociationField::new('category', 'Catégorie'),
            AssociationField::new('author', 'Auteur'),
            DateTimeField::new('createdAt', 'Créé le')->hideOnForm(),
            DateTimeField::new('publishedAt', 'Publié le'),
            TextField::new('picture', 'Image')->hideOnIndex(),
        ];
    }
}
