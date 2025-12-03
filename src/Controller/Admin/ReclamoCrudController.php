<?php

    namespace App\Controller\Admin;

    use App\Entity\Reclamo;
    use EasyCorp\Bundle\EasyAdminBundle\Attribute\AdminRoute;
    use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
    use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
    use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
    use EasyCorp\Bundle\EasyAdminBundle\Context\AdminContext;
    use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
    use EasyCorp\Bundle\EasyAdminBundle\Field\FormField;
    use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
    use EasyCorp\Bundle\EasyAdminBundle\Field\IntegerField;
    use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
    use EasyCorp\Bundle\EasyAdminBundle\Field\TextareaField;
    use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
    use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;
    use EasyCorp\Bundle\EasyAdminBundle\Config\Filters;
    use EasyCorp\Bundle\EasyAdminBundle\Filter\TextFilter;
    use EasyCorp\Bundle\EasyAdminBundle\Filter\ChoiceFilter;
    use Doctrine\Persistence\ManagerRegistry;
    use Knp\Bundle\SnappyBundle\Snappy\Response\PdfResponse;
    use Knp\Snappy\Pdf;
    use Symfony\Bridge\Twig\Mime\TemplatedEmail;
    use Symfony\Component\DependencyInjection\Attribute\Target;
    use Symfony\Component\HttpFoundation\Response;
    use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
    use Symfony\Component\Mailer\Mailer;
    use Symfony\Component\Mailer\MailerInterface;
    use Symfony\Component\Mime\Email;
    use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
    use Symfony\Component\Workflow\WorkflowInterface;
    use EasyCorp\Bundle\EasyAdminBundle\Router\AdminUrlGenerator;
    use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
    use Twig\Environment;
    use Symfony\Bundle\SecurityBundle\Security;
    use Doctrine\ORM\EntityManagerInterface;
    use EasyCorp\Bundle\EasyAdminBundle\Dto\SearchDto;
    use EasyCorp\Bundle\EasyAdminBundle\Dto\EntityDto;
    use EasyCorp\Bundle\EasyAdminBundle\Collection\FieldCollection;
    use EasyCorp\Bundle\EasyAdminBundle\Collection\FilterCollection;
    use Doctrine\ORM\QueryBuilder;
    use Symfony\Component\HttpFoundation\BinaryFileResponse;
    use Symfony\Component\HttpFoundation\ResponseHeaderBag;
    use Symfony\Component\HttpFoundation\RedirectResponse;
    use Symfony\Component\Validator\Constraints as Assert;
    use EasyCorp\Bundle\EasyAdminBundle\Field\Field;
    use Symfony\Component\Form\Extension\Core\Type\TextType;
    #[AdminRoute(path:('/reclamo/current'), name: 'reclamo_current')]
    class ReclamoCrudController extends AbstractCrudController
    {
        private ManagerRegistry $registry;
        private MailerInterface $mailer;
        private WorkflowInterface $workflow;
        private EntityManagerInterface $entityManager;
        private Security $security;
        private $adminUrlGenerator;
        Private $pdf;
        private $twig;


        public function __construct(
            ManagerRegistry $registry,
            MailerInterface $mailer,
            #[Target('atencion_reclamo')]WorkflowInterface $workflow,
            AdminUrlGenerator $adminUrlGenerator,
            Pdf $pdf,
            Environment $twig,
            EntityManagerInterface $entityManager,
            Security $security,

        )
        {
          $this->registry = $registry;
          $this->mailer = $mailer;
          $this->workflow = $workflow;
          $this->adminUrlGenerator = $adminUrlGenerator;
          $this->pdf = $pdf;
          $this->twig = $twig;
          $this->security = $security;
          $this->entityManager = $entityManager;

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
                    'Impreso' => 'Impreso',
                    'Atendiendo' => 'Atendiendo',
                    'En Guardia' => 'En Guardia',
                    'Creado' => 'Creado',
                    'Finalizado' => 'Finalizado',
                ]));
        }

        public function configureFields(string $pageName): iterable
        {
            return [
                IdField::new('id')->onlyOnIndex(),
                TextField::new('Servicio'),
                TextField::new('numeroCliente')
                    ->setFormTypeOptions([
                        'constraints' => [
                            new Assert\NotBlank([ 'message' => 'Ingrese el número de cliente' ]),
                            new Assert\Regex([ 'pattern' => '/^\d+$/', 'message' => 'Solo se permiten dígitos.' ]),
                            new Assert\Length([ 'max' => 12, 'maxMessage' => 'El número no puede tener más de {{ limit }} dígitos' ]),
                        ],
                        'help' => 'El número de cliente debe tener hasta 12 dígitos',
                    ]),
                TextField::new('numeroMedidor'),
                TextField::new('Domicilio'),
                TextField::new('Usuario'),
                TextField::new('Motivo'),
                TextareaField::new('Detalle')->hideOnIndex(),
                ChoiceField::new('estado')
                    ->setChoices([
                        'Impreso' => 'Impreso',
                        'Atendiendo' => 'Atendiendo',
                        'En Guardia' => 'En Guardia',
                        'Creado' => 'Creado',
                        'Finalizado' => 'Finalizado',
                    ])
                    ->renderAsBadges([
                        'Impreso' => 'warning',
                        'Atendiendo' => 'success',
                        'En Guardia' => 'info',
                        'Creado' => 'secondary',
                        'Finalizado' => 'danger',
                    ]),
                DateTimeField::new('fechaCreacion')
                    ->setFormat('dd/MM/yyyy HH:mm')
                    ->hideOnForm(),
                DateTimeField::new('fechaDeVisualizacion')
                    ->onlyOnDetail()
                    ->setLabel('Visualización'),

                DateTimeField::new('fechaCierre')
                    ->onlyOnDetail()
                    ->setLabel('Fecha de cierre'),

                TextField::new('userCierre')
                    ->onlyOnDetail()
                    ->setLabel('Cerrado por'),
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
            $cerrarAction = Action::new('cerrarReclamo', 'Cerrar Reclamo', 'fa fa-check-circle')
                ->linkToRoute('reclamo_close', function (Reclamo $reclamo): array {
                    return [
                        'reclamo' => $reclamo->getId(),
                    ];
                })
                ->setCssClass('btn btn-danger')
                ->displayIf(static function ($entity) use ($workflow) {
                    return $workflow->can($entity, 'to_close');
                });
            $derivarAction = Action::new('derivarReclamo', 'Derivar a guardia', 'fa fa-check-circle')
                ->linkToCrudAction('derivarReclamo')
                ->setCssClass('btn btn-primary')
                ->displayIf(static function ($entity) use ($workflow) {
                    return $workflow->can($entity, 'to_derived');
                });

            $pdfAction = Action::new('reclamoPDF', 'Generar pdf', 'fa fa-file-pdf')
                ->linkToCrudAction('reclamoPDF')
                ->setCssClass('btn btn-danger')
                ;

            $generateTicketsBatch = Action::new('reclamoPDFBatch', 'Generar Tickets')
                ->linkToCrudAction('reclamoPDFBatch')
                ->addCssClass('btn btn-warning')
                ->setIcon('fa fa-print');



            return $actions

                ->add(Crud::PAGE_INDEX, Action::DETAIL)
                ->add(Crud::PAGE_DETAIL, $derivarAction)
                ->add(Crud::PAGE_DETAIL, $atendidoAction)
                ->add(Crud::PAGE_DETAIL, $cerrarAction)
                ->add(Crud::PAGE_DETAIL, $pdfAction)
                ->addBatchAction($generateTicketsBatch)
                ->setPermission(Action::NEW, 'ROLE_RECLAMO_ADMIN')
                ->setPermission(Action::EDIT, 'ROLE_RECLAMO_ADMIN')
                ->setPermission(Action::DELETE, 'ROLE_RECLAMO_ADMIN')
                ;


        }

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
        #[AdminRoute(path: '/reclamo/cerrado/{id}', name: 'reclamo_cerrado')]
        public function cerrarReclamo(AdminContext $context): Response
        {
            $reclamo = $context->getEntity()->getInstance();

            // ✅ 1. APLICAMOS EL WORKFLOW
            $this->workflow->apply($reclamo, 'to_close');

            // ✅ 2. FECHA DE CIERRE AUTOMÁTICA
            $reclamo->setFechaCierre(new \DateTime());

            // ✅ 3. USUARIO QUE CERRÓ EL RECLAMO
            $usuario = $this->security->getUser();
            $reclamo->setUserCierre($usuario ? $usuario->getUserIdentifier() : 'Sistema');

            // ✅ 4. GUARDAMOS
            $this->entityManager->persist($reclamo);
            $this->entityManager->flush();

            // (opcional) mail
            $this->sendEmail($reclamo);

            $this->addFlash('success', sprintf(
                'El reclamo N°%d se cerró correctamente.',
                $reclamo->getId()
            ));

            return $this->redirectToRoute(
                'admin_reclamo_current_detail',
                ['entityId' => $reclamo->getId()]
            );
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

            if ($this->workflow->can($reclamo, 'to_derived')) {
                $this->workflow->apply($reclamo, 'to_derived');
            }

            $this->registry->getManager()->flush();

            $this->addFlash(
                'success',
                sprintf('El reclamo N° %d fue derivado correctamente a Guardia.', $reclamo->getId())
            );

            return $this->redirectToRoute('admin_reclamo_current_index');
        }



        public function sendEmail(Reclamo $reclamo): void
        {
            $email = (new TemplatedEmail())
                ->from('hello@example.com')
                ->to('you@example.com')
                ->subject('Time for Symfony Mailer!')
                ->text('Sending emails is fun again!')
                ->htmlTemplate('emails/notificacion.html.twig')
                ->context([
                    'reclamo' => $reclamo,
                ]);

            try {
                $this->mailer->send($email);
            } catch (TransportExceptionInterface $e) {
                dd($e);
            }
        }


        #[AdminRoute(path: '/reclamo/pdf', name: 'reclamo_pdf')]
        public function reclamoPDF(AdminContext $context) : PdfResponse
        {

            $reclamo = $context->getEntity()->getInstance();
            $html = $this->twig->render('pdf/reclamo-pdf.html.twig', [
                'reclamo' => $reclamo,
            ]);

            $options = [
                'enable-local-file-access' => true,
                'encoding' => 'utf-8',
                'print-media-type' => true,
                'margin-top' => '2mm',
                'margin-right' => '0mm',
                'margin-bottom' => '0mm',
                'margin-left' => '0mm',
                'no-stop-slow-scripts' => true,
                'orientation' => 'Landscape',
                'page-width' => '80mm',
                'page-height' => '150mm',
               ];

            $this->pdf->setOptions($options);

            return new PdfResponse(
                $this->pdf->getOutputFromHtml($html),
                'reclamo.pdf'
            );
        }

        #[AdminRoute(path: '/reclamo/pdf/batch', name: 'reclamo_pdf_batch')]
        public function reclamoPDFBatch(AdminContext $context, AdminUrlGenerator $adminUrlGenerator): PdfResponse|RedirectResponse
        {


            $selectedIds = $context->getRequest()->request->all('batchActionEntityIds');

            $reclamos = $this->entityManager
                ->getRepository(Reclamo::class)
                ->findBy(['id' => $selectedIds]);


            $html = $this->twig->render('pdf/reclamos-batch.html.twig', [
                'reclamos' => $reclamos,
            ]);

            $this->pdf->setOptions([
                'enable-local-file-access' => true,
                'encoding' => 'utf-8',
                'print-media-type' => true,
                'margin-top' => '2mm',
                'margin-right' => '0mm',
                'margin-bottom' => '0mm',
                'margin-left' => '0mm',
                'no-stop-slow-scripts' => true,
                'orientation' => 'Landscape',
                'page-width' => '80mm',
                'page-height' => '150mm',
            ]);

            $fecha = (new \DateTime())->format('d-m-Y_His');
            $filename = sprintf('reclamos_%s.pdf', $fecha);

            return new PdfResponse(
                $this->pdf->getOutputFromHtml($html),
                $filename
            );
        }

        public function createIndexQueryBuilder(SearchDto $searchDto, EntityDto $entityDto, FieldCollection $fields, FilterCollection $filters): QueryBuilder
        {
            $queryBuilder = parent::createIndexQueryBuilder($searchDto, $entityDto, $fields, $filters);
            $currentUser = $this->security->getUser();

            if (!$currentUser) {
                return $queryBuilder;
            }

            if ($this->security->isGranted('ROLE_RECLAMO_ADMIN')) {
                return $queryBuilder;
            }

            if ($this->security->isGranted('ROLE_RECLAMO_ATENCION_TELEFONISTA')) {
                return $queryBuilder;
            }
            if ($this->security->isGranted('ROLE_RECLAMO_CREACION')) {
                $queryBuilder
                    ->andWhere('entity.estado = :estado')
                    ->setParameter('estado', 'Creado');

                return $queryBuilder;
            }

            if ($this->security->isGranted('ROLE_RECLAMO_GUARDIA_ENERGIA')) {

                $queryBuilder
                    ->andWhere('entity.Servicio = :servicio')
                    ->setParameter('servicio', 'Energia');

                if (!array_key_exists('estado', $searchDto->getAppliedFilters())) {
                    $queryBuilder
                        ->andWhere('entity.estado = :estado')
                        ->setParameter('estado', 'En Guardia');
                }

                return $queryBuilder;
            }


            if ($this->security->isGranted('ROLE_RECLAMO_GUARDIA_SANEAMIENTO')) {

                $queryBuilder
                    ->andWhere('entity.Servicio IN (:servicios)')
                    ->setParameter('servicios', ['Agua', 'Cloaca']);

                if (!array_key_exists('estado', $searchDto->getAppliedFilters())) {
                    $queryBuilder
                        ->andWhere('entity.estado = :estado')
                        ->setParameter('estado', 'En Guardia');
                }

                return $queryBuilder;
            }






            return $queryBuilder;

        }

    }

