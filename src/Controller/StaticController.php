<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class StaticController extends AbstractController
{

    #[Route("/", name: "static")]
    public function index(): Response
    {
        return $this->render('static/index.html.twig', [
            'controller_name' => 'Bienvenue',
        ]);
    }
}
