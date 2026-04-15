<?php

namespace App\Controller\Admin;

use App\Entity\User;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\EmailField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ImageField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserCrudController extends AbstractCrudController
{
    public function __construct(
        private UserPasswordHasherInterface $passwordHasher
    ) {}

    public static function getEntityFqcn(): string
    {
        return User::class;
    }

    public function configureFields(string $pageName): iterable
    {
        yield TextField::new('username', 'Pseudo');
        yield TextField::new('firstName', 'Prénom');
        yield TextField::new('lastName', 'Nom');
        yield EmailField::new('email', 'Email');

        // Champ mot de passe uniquement en création/édition
        yield TextField::new('password', 'Mot de passe')
            ->setRequired(false)
            ->onlyOnForms();

        yield ChoiceField::new('roles', 'Rôles')
            ->setChoices([
                'Utilisateur' => 'ROLE_USER',
                'Administrateur' => 'ROLE_ADMIN',
            ])
            ->allowMultipleChoices()
            ->renderExpanded(false)
            ->setRequired(false);


        yield ImageField::new('profilePicture', 'Photo de profil')
            ->setBasePath('uploads/profiles')
            ->setUploadDir('public/uploads/profiles')
            ->setRequired(false)
            ->hideOnIndex();
        yield BooleanField::new('isActive', 'Compte actif')
            ->renderAsSwitch(true);

        yield DateTimeField::new('createdAt', 'Inscrit le')
            ->onlyOnIndex();
    }

    // Hash le mot de passe avant persist (création)
    public function persistEntity(EntityManagerInterface $entityManager, mixed $entityInstance): void
    {
        $this->hashPassword($entityInstance);

        // isActive et createdAt par défaut
        if ($entityInstance->getCreatedAt() === null) {
            $entityInstance->setCreatedAt(new \DateTimeImmutable());
        }
        if ($entityInstance->isActive() === null) {
            $entityInstance->setIsActive(true);
        }
//        dd($entityInstance->getPassword());
        parent::persistEntity($entityManager, $entityInstance);
    }

    // Hash le mot de passe avant update (édition)
    public function updateEntity(EntityManagerInterface $entityManager, mixed $entityInstance): void
    {
        $newPassword = $entityInstance->getPassword();

        if (empty($newPassword)) {
            // Recharge l'ancienne valeur depuis la base
            $original = $entityManager->getUnitOfWork()->getOriginalEntityData($entityInstance);
            $entityInstance->setPassword($original['password']);
        } else {
            $hashed = $this->passwordHasher->hashPassword($entityInstance, $newPassword);
            $entityInstance->setPassword($hashed);
        }
        $entityManager->getUnitOfWork()->recomputeSingleEntityChangeSet(
            $entityManager->getClassMetadata(User::class),
            $entityInstance
        );

        parent::updateEntity($entityManager, $entityInstance);
    }

    private function hashPassword(User $user): void
    {
        $plainPassword = $user->getPassword();
        if (empty($plainPassword)) {
            return;
        }
        $hashed = $this->passwordHasher->hashPassword($user, $plainPassword);
        $user->setPassword($hashed);
    }
}
