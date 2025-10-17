<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class MainController extends AbstractController
{
    #[Route('/main', name: 'app_main')]
    public function index(): Response
    {
        return $this->render('main/index.html.twig', [
            'controller_name' => 'MainController',
        ]);
    }
}
/*public function atencionReclamo(EntityManagerInterface $entityManager): Response
    {
        $reclamoId = $this->getContext()->getRequest()->query->get('entityId');

        $reclamo = $entityManager->getRepository(Reclamo::class)->find($reclamoId);

        if (!$reclamo) {
            throw $this->createNotFoundException('Reclamo no encontrado');
        }

        // Cambiar el estado a "Atendido"
        $reclamo->setEstado('Atendido');
        $entityManager->flush();

        // Agregar mensaje flash de éxito
        $this->addFlash('success', 'El reclamo ha sido marcado como atendido correctamente.');

        // Redirigir a la vista de detalle del reclamo
        return $this->redirectToRoute('admin', [
            'crudAction' => 'detail',
            'crudControllerFqcn' => self::class,
            'entityId' => $reclamoId,
        ]);
    }*/
