<?php

namespace App\Controller\Admin;

use App\Entity\Booking;
use App\Entity\DateBlocked;
use App\Entity\Meeting;
use App\Entity\Paypal;
use App\Entity\User;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Dashboard;
use EasyCorp\Bundle\EasyAdminBundle\Config\MenuItem;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractDashboardController;

class DashboardController extends AbstractDashboardController
{
    public function configureDashboard(): Dashboard
    {
        return Dashboard::new()
            ->setTitle('Admin Réservation');
    }

    public function configureCrud(): Crud
    {
        return Crud::new()
            ->setDateFormat('dd/MM/yyyy');
    }

    public function configureMenuItems(): iterable
    {

        yield MenuItem::section('Réservation', 'fas fa-folder-open');
        yield MenuItem::linkToUrl('Nouvelle Réservation', 'fas fa-plus', $this->generateUrl('new_reservation'));
        yield MenuItem::linkToCrud('Réservation', 'fas fa-calendar-alt', Booking::class)->setDefaultSort(['bookingDate' => 'DESC']);

        yield MenuItem::section('Autres', 'fas fa-folder-open');
        yield MenuItem::linkToCrud('Client', 'fas fa-user', User::class)->setDefaultSort(['name' => 'ASC']);
        yield MenuItem::linkToCrud('Séance', 'fas fa-clock', Meeting::class)->setDefaultSort(['id' => 'ASC']);
        yield MenuItem::linkToCrud('Bloqué une date', 'fas fa-clock', DateBlocked::class)->setDefaultSort(['start' => 'DESC']);
        yield MenuItem::linkToCrud('Paiement', 'fas fa-folder-open', Paypal::class);
    }
}
