<?php

namespace App\Controller\Admin;

use App\Entity\Meeting;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\Field;
use EasyCorp\Bundle\EasyAdminBundle\Field\IntegerField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

class MeetingCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Meeting::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setSearchFields(['id', 'label']);
    }

    public function configureFields(string $pageName): iterable
    {
        $label = TextField::new('label');
        $isActive = Field::new('isActive');
        $room = AssociationField::new('room');
        $id = IntegerField::new('id', 'ID');

        if (Crud::PAGE_INDEX === $pageName) {
            return [$id, $label, $isActive, $room];
        } elseif (Crud::PAGE_DETAIL === $pageName) {
            return [$id, $label, $isActive, $room];
        } elseif (Crud::PAGE_NEW === $pageName) {
            return [$label, $isActive, $room];
        } elseif (Crud::PAGE_EDIT === $pageName) {
            return [$label, $isActive, $room];
        }
    }
}
