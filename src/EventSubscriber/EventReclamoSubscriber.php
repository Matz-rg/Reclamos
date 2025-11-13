<?php

namespace App\EventSubscriber;

use App\Entity\Reclamo;
use Doctrine\ORM\EntityManagerInterface;
use EasyCorp\Bundle\EasyAdminBundle\Event\BeforeCrudActionEvent;
use EasyCorp\Bundle\EasyAdminBundle\Context\AdminContext;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Workflow\WorkflowInterface;

class EventReclamoSubscriber implements EventSubscriberInterface
{
    private Security $security;
    private WorkflowInterface $reclamoStateMachine;
    private EntityManagerInterface $em;

    public function __construct(Security $security, WorkflowInterface $reclamoStateMachine, EntityManagerInterface $em)
    {
        $this->security = $security;
        $this->reclamoStateMachine = $reclamoStateMachine;
        $this->em = $em;
    }

    public static function getSubscribedEvents(): array
    {
        return [
            BeforeCrudActionEvent::class => ['onBeforeCrudAction'],
        ];
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

}
