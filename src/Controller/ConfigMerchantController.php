<?php

namespace App\Controller;

use App\Entity\ConfigMerchant;
use App\Form\ConfigMerchantType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ConfigMerchantController extends AbstractController
{
    /**
     * @Route("/config", name="config_merchant")
     * @param Request $request
     * @param EntityManagerInterface $em
     * @return Response
     */
    public function index(Request $request, EntityManagerInterface $em): Response
    {
        $configMerchantRepo = $em->getRepository(ConfigMerchant::class);

        if (!empty($config = $configMerchantRepo->findAll())) {
            $config = $config[0];
        } else {
            $config = new ConfigMerchant();
        }

        $form = $this->createForm(ConfigMerchantType::class, $config);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($config);
            $em->flush();
            $this->addFlash('success', 'Votre configuration marchand a bien été enregistrée');
        }

        return $this->render('config_merchant/index.html.twig', ['form' => $form->createView()]);
    }
}
