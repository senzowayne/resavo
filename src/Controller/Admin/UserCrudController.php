<?php

namespace App\Controller\Admin;

use App\Entity\User;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IntegerField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

class UserCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return User::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setPageTitle(Crud::PAGE_INDEX, 'Client')
            ->setSearchFields(['name', 'email', 'number']);
    }

    public function configureFields(string $pageName): iterable
    {
        $name = TextField::new('name', 'Nom');
        $firstName = TextField::new('firstName', 'Prénom');
        $email = TextField::new('email');
        $number = TextField::new('number', 'Téléphone');
        $hash = TextField::new('hash','Mot de passe');
        $id = IntegerField::new('id', 'ID');
        $avatar = TextField::new('avatar');
        $googleId = TextField::new('googleId');
        $bookings = AssociationField::new('bookings', 'Réservations');
        $payments = AssociationField::new('payments', 'Paiements');
        $userRoles = AssociationField::new('userRoles', 'Roles');

        if (Crud::PAGE_INDEX === $pageName) {
            return [$name, $firstName, $email, $bookings, $number];
        } elseif (Crud::PAGE_DETAIL === $pageName) {
            return [$id, $name, $firstName, $email, $avatar, $hash, $number, $googleId, $bookings, $payments, $userRoles];
        } elseif (Crud::PAGE_NEW === $pageName) {
            return [$name, $firstName, $email, $number, $hash];
        } elseif (Crud::PAGE_EDIT === $pageName) {
            return [$name, $firstName, $email, $number];
        }
    }
}
