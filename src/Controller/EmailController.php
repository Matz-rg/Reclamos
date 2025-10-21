<?php

namespace App\Controller;

use Symfony\Bridge\Twig\Mime\TemplatedEmail;

$email = (new TemplatedEmail())
    ->from('fabien@example.com')
    ->subject('Thanks for signing up!')

    // path of the Twig template to render
    ->htmlTemplate('emails/notificacion.html.twig')


    // change locale used in the template, e.g. to match user's locale
    ->locale('de')

    // pass variables (name => value) to the template
    ->context([
        'expiration_date' => new \DateTime('+7 days'),
        'username' => 'Matias',
    ])
;
