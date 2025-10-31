<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class WebController extends AbstractController
{
    #[Route('/', name: 'app_homepage')]
    public function index(): Response
    {
        return $this->render('web/index.html.twig', [
            'cont roller_name' => 'WebController',
            'title_name_service' => 'Sistema de Reclamos por Servicios',

        ]);
    }
}
