<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class MainController extends AbstractController
{
    /**
     * @Route("/", name="accueil")
     */
    public function index()
    {
        // Nous générons la vue de la page d'accueil
        return $this->render('main/index.html.twig', [
            'controller_name' => 'MainController',
        ]);
    }
}
