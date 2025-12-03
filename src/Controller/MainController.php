<?php

namespace App\Controller;

use App\Entity\Reclamo;
use App\Form\CierreReclamoType;
use Doctrine\ORM\EntityManagerInterface;
use EasyCorp\Bundle\EasyAdminBundle\Router\AdminUrlGenerator;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\DependencyInjection\Attribute\Target;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Workflow\Workflow;
use Symfony\Component\Workflow\WorkflowInterface;


final class MainController extends AbstractController
{
    private AdminUrlGenerator $adminUrlGenerator;
    private EntityManagerInterface $entityManager;
    private WorkflowInterface $workflowInterface;

    public function __construct(AdminUrlGenerator $urlGenerator, AdminUrlGenerator $adminUrlGenerator, EntityManagerInterface $entityManager, #[Target('atencion_reclamo')]WorkflowInterface $workflow,)
    {
        $this->adminUrlGenerator = $adminUrlGenerator;

        $this->entityManager = $entityManager;

        $this->workflowInterface = $workflow;
    }

    #[Route('/main', name: 'app_main')]
    public function index(): Response
    {
        return $this->render('main/index.html.twig', [
            'controller_name' => 'MainController',
        ]);
    }
    #[Route('/admin/reclamo/close/{reclamo}', name: 'reclamo_close')]
    public function closeReclamo(Request $request, Reclamo $reclamo, EntityManagerInterface $em, Security $security): Response
    {
        $reclamo->setFechaCierre(new \DateTime());

        $reclamo->setUserCierre($this->getUser()->getUserIdentifier());

        $em->flush();

        $url = $this->adminUrlGenerator
            ->setRoute(
                'admin_reclamo_current_detail',
                ['entityId' => $reclamo->getId()]
            )
            ->generateUrl();

        //Creacion del formulario
        $form = $this->createForm(CierreReclamoType::class, $reclamo);

        //Proceso del formulario
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

           // $this->entityManager->persist($reclamo);
            $this->workflowInterface->apply($reclamo, 'to_close');

            $this->entityManager->flush();

            $this->addFlash('success', 'El reclamo fue cerrado correctamente.');

            return $this->redirect($url);
        }

        //Visualizacion del formulario
        return $this->render('causa/causaDeReclamo.html.twig', [
            'form' => $form,
            'reclamo' => $reclamo,
            'volver_al_detalle' => $url,
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
