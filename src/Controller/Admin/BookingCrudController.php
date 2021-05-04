<?php

namespace App\Controller\Admin;

use App\Entity\Booking;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\FormField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IntegerField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TelephoneField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

class BookingCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Booking::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityLabelInSingular('Booking')
            ->setEntityLabelInPlural('Booking')
            ->setSearchFields(['user.nom', 'user.email', 'user.number', 'bookingDate', 'name', 'user'])
            ->showEntityActionsAsDropdown();
    }

    public function configureFields(string $pageName): iterable
    {
        $panel1 = FormField::addPanel('Basic information');
        $name = TextField::new('name', 'ID');
        $user = AssociationField::new('user');
        $total = TextField::new('total');
        $notices = TextField::new('notices');
        $panel2 = FormField::addPanel('Information réservation');
        $bookingDate = DateField::new('bookingDate');
        $room = AssociationField::new('room');
        $meeting = AssociationField::new('meeting');
        $nbPerson = IntegerField::new('nbPerson');
        $id = IntegerField::new('id', 'ID');
        $createAt = DateTimeField::new('createAt');
        $payment = AssociationField::new('payment');
        $userNumber = TelephoneField::new('user.number');

        if (Crud::PAGE_INDEX === $pageName) {
            return [$name, $createAt, $bookingDate, $user, $room, $meeting, $nbPerson, $userNumber, $payment, $total, $notices];
        } elseif (Crud::PAGE_DETAIL === $pageName) {
            return [$id, $createAt, $bookingDate, $nbPerson, $name, $notices, $total, $user, $room, $meeting, $payment];
        } elseif (Crud::PAGE_NEW === $pageName) {
            return [$panel1, $name, $user, $total, $notices, $panel2, $bookingDate, $room, $meeting, $nbPerson];
        } elseif (Crud::PAGE_EDIT === $pageName) {
            return [$panel1, $name, $user, $total, $notices, $panel2, $bookingDate, $room, $meeting, $nbPerson];
        }
    }
}
