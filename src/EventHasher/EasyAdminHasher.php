<?php

namespace App\EventHasher;

use App\Entity\User;
use EasyCorp\Bundle\EasyAdminBundle\Event\BeforeEntityPersistedEvent;
use EasyCorp\Bundle\EasyAdminBundle\Event\BeforeEntityUpdatedEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Twig\Environment;

class EasyAdminHasher implements EventSubscriberInterface
{
    private UserPasswordHasherInterface $passwordHasher;
    private MailerInterface $mailer;
    private Environment $twig;
    public function __construct(UserPasswordHasherInterface $passwordHasher, MailerInterface $mailer, Environment $twig)
    {
        $this->passwordHasher = $passwordHasher;
        $this->mailer = $mailer;
        $this->twig = $twig;
    }

    public static function getSubscribedEvents(): array
    {
        return [
            BeforeEntityPersistedEvent::class => ['onBeforeEntityPersisted'],
            BeforeEntityUpdatedEvent::class => ['onBeforeEntityUpdated'],
        ];
    }

    public function onBeforeEntityPersisted(BeforeEntityPersistedEvent $event): void
    {
        $entity = $event->getEntityInstance();

        if (!($entity instanceof User)) {
            return;
        }

        $plainPassword = $entity->getPassword();

        // Hashear la contraseña solo si el admin escribió algo
        if ($entity->getPassword()) {
            $hashedPassword = $this->passwordHasher->hashPassword($entity, $entity->getPassword());
            $entity->setPassword($hashedPassword);
            $this->sendWelcomeEmail($entity, $plainPassword);
        }
    }

    public function onBeforeEntityUpdated(BeforeEntityUpdatedEvent $event): void
    {
        $entity = $event->getEntityInstance();

        if (!($entity instanceof User)) {
            return;
        }

        if (strlen($entity->getPassword()) < 60) {
            $plainPassword = $entity->getPassword();
            $hashedPassword = $this->passwordHasher->hashPassword($entity, $entity->getPassword());
            $entity->setPassword($hashedPassword);

            $this->sendWelcomeEmail($entity, $plainPassword);
        }
    }
    private function sendWelcomeEmail(User $user, string $plainPassword): void
    {

        $htmlContent = $this->twig->render('emails/accountUserCredentials.html.twig', [
            'email' => $user->getEmail(),
            'password' => $plainPassword,
        ]);

        $email = (new Email())
            ->from('no-reply@sistema-reclamos.com')
            ->to($user->getEmail())
            ->subject('Tu cuenta ha sido creada en el Sistema de Reclamos')
            ->html($htmlContent);

        $this->mailer->send($email);
    }
}
