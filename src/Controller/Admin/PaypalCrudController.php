<?php

namespace App\Controller\Admin;

use App\Entity\Paypal;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\Field;
use EasyCorp\Bundle\EasyAdminBundle\Field\IntegerField;
use EasyCorp\Bundle\EasyAdminBundle\Field\NumberField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextareaField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

class PaypalCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Paypal::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setSearchFields(['id', 'payment_id', 'payment_status', 'payment_amount', 'payment_currency', 'payer_email', 'captureId']);
    }

    public function configureFields(string $pageName): iterable
    {
        $paymentId = TextField::new('payment_id', 'ID');
        $paymentStatus = TextareaField::new('payment_status','Statut');
        $paymentAmount = NumberField::new('payment_amount', 'Prix');
        $paymentCurrency = TextareaField::new('payment_currency', 'Devise');
        $paymentDate = DateTimeField::new('payment_date', 'Crée le');
        $payerEmail = TextareaField::new('payer_email', 'Email');
        $capture = Field::new('capture', 'Encaissé');
        $captureId = TextField::new('captureId', 'ID encaissement');
        $user = AssociationField::new('user', 'Client');
        $booking = AssociationField::new('booking', 'Reservation');
        $id = IntegerField::new('id', 'ID')->hideOnIndex();

        if (Crud::PAGE_INDEX === $pageName) {
            return [$id, $paymentId, $paymentAmount, $paymentDate, $capture, $captureId, $user];
        } elseif (Crud::PAGE_DETAIL === $pageName) {
            return [$id, $paymentId, $paymentStatus, $paymentAmount, $paymentCurrency, $paymentDate, $payerEmail, $capture, $captureId, $user, $booking];
        } elseif (Crud::PAGE_NEW === $pageName) {
            return [$paymentId, $paymentStatus, $paymentAmount, $paymentCurrency, $paymentDate, $payerEmail, $capture, $captureId, $user, $booking];
        } elseif (Crud::PAGE_EDIT === $pageName) {
            return [$paymentId, $paymentStatus, $paymentAmount, $paymentCurrency, $paymentDate, $payerEmail, $capture, $captureId, $user, $booking];
        }
    }
}
