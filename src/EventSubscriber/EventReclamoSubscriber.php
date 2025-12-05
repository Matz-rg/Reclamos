<?php

namespace App\EventSubscriber;

use App\Entity\Reclamo;
use Doctrine\ORM\EntityManagerInterface;
use EasyCorp\Bundle\EasyAdminBundle\Event\BeforeCrudActionEvent;
use EasyCorp\Bundle\EasyAdminBundle\Context\AdminContext;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Workflow\WorkflowInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class EventReclamoSubscriber implements EventSubscriberInterface
{
    private Security $security;
    private WorkflowInterface $reclamoStateMachine;
    private EntityManagerInterface $em;
    private HttpClientInterface $http;

    public function __construct(Security $security, WorkflowInterface $reclamoStateMachine, EntityManagerInterface $em,  HttpClientInterface $http)
    {
        $this->security = $security;
        $this->reclamoStateMachine = $reclamoStateMachine;
        $this->em = $em;
        $this->http = $http;
    }

    public static function getSubscribedEvents(): array
    {
        return [
            BeforeCrudActionEvent::class => [
                ['onBeforeCrudAction', 0],
                ['onCreateReclamoSiniestro', 0]
            ],
        ];
    }
    public function obtenerNumeroMedidor(): array
    {
        $response = $this->http->request(
            'GET',
            'https://apivt.spse.app/api/maestros?{nrocliente}'
        );


        $content = $response->toArray();

        $lista = [];
        foreach ($content['hydra:member'] as $item) {
            $lista[] = (string) $item['medidor'];
        }
        dd(array_column($content, 'medidor'));
        //return $lista;
    }

    public function onBeforeCrudAction(BeforeCrudActionEvent $event): void
    {
        $context = $event->getAdminContext();
        if (!$context instanceof AdminContext) {
            return;
        }


        if ($context->getCrud()->getCurrentAction() !== 'detail') {
            return;
        }

        $entity = $context->getEntity()->getInstance();
        if (!$entity instanceof Reclamo) {
            return;
        }

        $user = $this->security->getUser();
        if (!$user) {
            return;
        }

            if ($this->reclamoStateMachine->can($entity, 'to_success')) {
                $this->reclamoStateMachine->apply($entity, 'to_success');
                $this->em->persist($entity);
                $this->em->flush();
            }
    }
    public function onCreateReclamoSiniestro(BeforeCrudActionEvent $event): void  {


        $entity = $event->getAdminContext()->getEntity();
        if (!$entity instanceof Reclamo) {
            return;
        }

        if($entity->getSiniestro()) {
            if ($this->reclamoStateMachine->can($entity, 'to_siniestro')) {
                $this->reclamoStateMachine->apply($entity, 'to_siniestro');
                $this->em->persist($entity);
                $this->em->flush();
            }
        }

    }



}
