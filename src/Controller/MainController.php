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
use Symfony\Component\Workflow\WorkflowInterface;

final class MainController extends AbstractController
{
    private AdminUrlGenerator $adminUrlGenerator;
    private EntityManagerInterface $entityManager;
    private WorkflowInterface $workflowInterface;

    public function __construct(AdminUrlGenerator $adminUrlGenerator, EntityManagerInterface $entityManager, #[Target('atencion_reclamo')] WorkflowInterface $workflow)
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

    #[Route('/admin/reclamo/close/{reclamo}', name: 'reclamo_close' )]
    public function closeReclamo(Request $request, Reclamo $reclamo): Response
    {
        // Set closure date and user
        $reclamo->setFechaCierre(new \DateTime());
        $reclamo->setUserCierre($this->getUser()->getUserIdentifier());

        // Create the form
        $form = $this->createForm(CierreReclamoType::class, $reclamo);

        // Process the form
        $form->handleRequest($request);

        $url = $this->adminUrlGenerator
            ->setRoute(
                'admin_reclamo_current_detail',
                ['entityId' => $reclamo->getId()]
            )
            ->generateUrl();

        if ($form->isSubmitted() && $form->isValid()) {
            // Apply workflow transition
            $this->workflowInterface->apply($reclamo, 'to_close');

            // Persist changes
            $this->entityManager->flush();

            $this->addFlash('success', 'El reclamo fue cerrado correctamente.');

            return $this->redirect($url);
        }

        // Display the form
        return $this->render('causa/causaDeReclamo.html.twig', [
            'form' => $form->createView(),
            'reclamo' => $reclamo,
            'volver_al_detalle' => $url,
        ]);
    }
}
