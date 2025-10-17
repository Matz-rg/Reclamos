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
use Symfony\Component\HttpFoundation\Response;

#[AdminRoute(path:('/reclamo/current'), name: 'reclamo_current')]
class ReclamoCrudController extends AbstractCrudController
{
    private ManagerRegistry $registry;
    public function __construct(ManagerRegistry $registry)
    {
      $this->registry = $registry;
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
                ])
                ->renderAsBadges([
                    'Pendiente' => 'warning',
                    'Atendido' => 'success',
                    'En Proceso' => 'info',
                ]),
            DateTimeField::new('fechaCreacion')
                ->setFormat('dd/MM/yyyy HH:mm')
                ->hideOnForm(),
        ];
    }

    public function configureActions(Actions $actions): Actions
    {
        $atencionAction = Action::new('atencion', 'Atención', 'fa fa-check-circle')
            ->linkToCrudAction('atencionReclamo')
            ->setCssClass('btn btn-success')
            ->displayIf(fn(Reclamo $reclamo) => $reclamo->getEstado() !== 'Atendido');

        return $actions

            ->add(Crud::PAGE_INDEX, Action::DETAIL)

            ->add(Crud::PAGE_DETAIL, $atencionAction)

            ->update(Crud::PAGE_INDEX, Action::DETAIL, function (Action $action) {
                return $action->setLabel('Ver')->setIcon('fa fa-eye');
            })
            ->update(Crud::PAGE_INDEX, Action::EDIT, function (Action $action) {
                return $action->setLabel('Editar')->setIcon('fa fa-edit');
            })
            ->update(Crud::PAGE_INDEX, Action::DELETE, function (Action $action) {
                return $action->setLabel('Eliminar')->setIcon('fa fa-trash');
            });
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
    #[AdminRoute(path: '/reclamo/atencion', name: 'reclamo_atencion')]
    public function atencionReclamo(AdminContext $context): Response
    {

        $reclamo = $context->getEntity()->getInstance();
        $reclamo->setEstado('Atendido');

        $this->registry->getManager()->persist($reclamo);
        $this->registry->getManager()->flush();

        return $this->redirectToRoute('admin_reclamo_current_detail', ['entityId' => $reclamo->getId()]);

    }
}
