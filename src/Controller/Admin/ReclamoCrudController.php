<?php

namespace App\Controller\Admin;

use App\Entity\Reclamo;
use EasyCorp\Bundle\EasyAdminBundle\Attribute\AdminRoute;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Context\AdminContext;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextareaField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;
use EasyCorp\Bundle\EasyAdminBundle\Config\Filters;
use EasyCorp\Bundle\EasyAdminBundle\Filter\TextFilter;
use EasyCorp\Bundle\EasyAdminBundle\Filter\ChoiceFilter;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\DependencyInjection\Attribute\Target;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Mailer\Mailer;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Component\Workflow\WorkflowInterface;
use Symfony\Config\Framework\WorkflowsConfig;

#[AdminRoute(path:('/reclamo/current'), name: 'reclamo_current')]
class ReclamoCrudController extends AbstractCrudController
{
    private ManagerRegistry $registry;
    private MailerInterface $mailer;
    private WorkflowInterface $workflow;
    public function __construct(ManagerRegistry $registry, MailerInterface $mailer, #[Target('atencion_reclamo')]WorkflowInterface $workflow)
    {
      $this->registry = $registry;
      $this->mailer = $mailer;
      $this->workflow = $workflow;
    }

    public static function getEntityFqcn(): string
    {
        return Reclamo::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityLabelInSingular('Reclamo')
            ->setEntityLabelInPlural('Reclamos')
            ->setSearchFields(['Servicio', 'numeroCliente', 'Domicilio', 'Usuario', 'Motivo'])
            ->setDefaultSort(['id' => 'DESC'])
            ->showEntityActionsInlined();
    }

    public function configureFilters(Filters $filters): Filters
    {
        return $filters
            ->add('id')
            ->add(TextFilter::new('Servicio'))
            ->add(TextFilter::new('numeroCliente'))
            ->add(TextFilter::new('numeroMedidor'))
            ->add(TextFilter::new('Domicilio'))
            ->add(TextFilter::new('Usuario'))
            ->add(TextFilter::new('Motivo'))
            ->add(ChoiceFilter::new('estado')->setChoices([
                'Pendiente' => 'Pendiente',
                'Atendido' => 'Atendido',
                'En Proceso' => 'En Proceso',
                'Creado' => 'Creado',
            ]));
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new('id')->onlyOnIndex(),
            TextField::new('Servicio'),
            TextField::new('numeroCliente'),
            TextField::new('numeroMedidor'),
            TextField::new('Domicilio'),
            TextField::new('Usuario'),
            TextField::new('Motivo'),
            TextareaField::new('Detalle')->hideOnIndex(),
            ChoiceField::new('estado')
                ->setChoices([
                    'Pendiente' => 'Pendiente',
                    'Atendido' => 'Atendido',
                    'En Proceso' => 'En Proceso',
                    'Creado' => 'Creado',
                ])
                ->renderAsBadges([
                    'Pendiente' => 'warning',
                    'Atendido' => 'success',
                    'En Proceso' => 'info',
                    'Creado' => 'secondary',
                ]),
            DateTimeField::new('fechaCreacion')
                ->setFormat('dd/MM/yyyy HH:mm')
                ->hideOnForm(),
        ];
    }

    public function configureActions(Actions $actions): Actions
    {
        $workflow = $this->workflow;

        $atendidoAction = Action::new('atencionReclamo', 'Atendido', 'fa fa-check-circle')
            ->linkToCrudAction('atencionReclamo')
            ->setCssClass('btn btn-success')
            ->displayIf(static function ($entity) use ($workflow) {
                return $workflow->can($entity, 'to_success');
            });
        $cerrarAction = Action::new('cerrarReclamo', 'Atendido', 'fa fa-check-circle')
            ->linkToCrudAction('cerrarReclamo')
            ->setCssClass('btn btn-success')
            ->displayIf(static function ($entity) use ($workflow) {
                return $workflow->can($entity, 'to_close');
            });
        $pendingAction = Action::new('pendingReclamo', 'Pendiente', 'fa fa-check-circle')
            ->linkToCrudAction('pendingReclamo')
            ->setCssClass('btn btn-primary')
            ->displayIf(static function ($entity) use ($workflow) {
                return $workflow->can($entity, 'to_pending');
            });

        $derivarAction = Action::new('derivarReclamo', 'Derivar a guardia', 'fa fa-check-circle')
            ->linkToCrudAction('derivarReclamo')
            ->setCssClass('btn btn-primary')
            ->displayIf(static function ($entity) use ($workflow) {
                return $workflow->can($entity, 'to_derived');
            });





        return $actions

            ->add(Crud::PAGE_INDEX, Action::DETAIL)
            ->add(Crud::PAGE_DETAIL, $pendingAction)
            ->add(Crud::PAGE_DETAIL, $derivarAction)
            ->add(Crud::PAGE_DETAIL, $atendidoAction)
            ->add(Crud::PAGE_DETAIL, $cerrarAction)
            ;

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



    #[AdminRoute(path: '/reclamo/atendido', name: 'reclamo_atendido')]
    public function atencionReclamo(AdminContext $context): Response
    {

        $reclamo = $context->getEntity()->getInstance();

        $this->workflow->apply($reclamo, 'to_success');
        $this->registry->getManager()->persist($reclamo);
        $this->registry->getManager()->flush();
        $this->sendEmail($reclamo);

        $this->addFlash('success', sprintf('El reclamo N°%d fue marcado como "Atendido".', $reclamo->getId()));

        return $this->redirectToRoute('admin_reclamo_current_detail', ['entityId' => $reclamo->getId()]);

    }

    #[AdminRoute(path: '/reclamo/cerrar', name: 'reclamo_cerrar')]
    public function cerrarReclamo(AdminContext $context): Response
    {

        $reclamo = $context->getEntity()->getInstance();

        $this->workflow->apply($reclamo, 'to_close');
        $this->registry->getManager()->persist($reclamo);
        $this->registry->getManager()->flush();
        $this->sendEmail($reclamo);

        $this->addFlash('success', sprintf('El reclamo N°%d fue marcado como "Atendido".', $reclamo->getId()));

        return $this->redirectToRoute('admin_reclamo_current_detail', ['entityId' => $reclamo->getId()]);

    }
    #[AdminRoute(path: '/reclamo/pendiente', name: 'reclamo_pendiente')]
    public function pendingReclamo(AdminContext $context): Response
    {
        $reclamo = $context->getEntity()->getInstance();

        $this->workflow->apply($reclamo, 'to_pending');
        $this->registry->getManager()->persist($reclamo);
        $this->registry->getManager()->flush();

        $this->addFlash('success', sprintf('El reclamo N°%d fue marcado como "Pendiente".', $reclamo->getId()));

        return $this->redirectToRoute('admin_reclamo_current_detail', ['entityId' => $reclamo->getId()]);
    }

    #[AdminRoute(path: '/reclamo/derivar', name: 'reclamo_derivar')]
    public function derivarReclamo(AdminContext $context): Response
    {
        $reclamo = $context->getEntity()->getInstance();

        $this->workflow->apply($reclamo, 'to_derived');
        $this->registry->getManager()->persist($reclamo);
        $this->registry->getManager()->flush();

        $this->addFlash('info', sprintf('El reclamo N°%d fue derivado correctamente.', $reclamo->getId()));

        return $this->redirectToRoute('admin_reclamo_current_detail', ['entityId' => $reclamo->getId()]);
    }



    public function sendEmail(Reclamo $reclamo): void
    {
        $email = (new TemplatedEmail())
            ->from('hello@example.com')
            ->to('you@example.com')
            ->subject('Time for Symfony Mailer!')
            ->text('Sending emails is fun again!')
            ->htmlTemplate('emails/notificacion.html.twig')
            ->textTemplate('emails/notificacion.txt.twig')
            ->attach()
            ->context([
                'reclamo' => $reclamo,

           ])
           ;

        try {
            $this->mailer->send($email);
        } catch (TransportExceptionInterface $e) {
            dd($e);
        }

    }
}
