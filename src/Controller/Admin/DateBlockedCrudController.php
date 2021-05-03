<?php

namespace App\Controller\Admin;

use App\Entity\DateBlocked;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IntegerField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

class DateBlockedCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return DateBlocked::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setSearchFields(['id', 'cause']);
    }

    public function configureFields(string $pageName): iterable
    {
        $cause = TextField::new('cause');
        $start = DateField::new('start');
        $end = DateField::new('end');
        $id = IntegerField::new('id', 'ID');

        if (Crud::PAGE_INDEX === $pageName) {
            return [$id, $cause, $start, $end];
        } elseif (Crud::PAGE_DETAIL === $pageName) {
            return [$id, $cause, $start, $end];
        } elseif (Crud::PAGE_NEW === $pageName) {
            return [$cause, $start, $end];
        } elseif (Crud::PAGE_EDIT === $pageName) {
            return [$cause, $start, $end];
        }
    }
}
