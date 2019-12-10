<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;


class StaticController extends AbstractController
{
    /**
     * @Route("/reservation/", name="static")
     */
    public function index()
    {
        return $this->render('static/index.html.twig', [
            'controller_name' => 'Bienvenue',
        ]);
    }
}
