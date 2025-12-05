<?php

namespace App\Story;

use App\Entity\Siniestro;
use App\Entity\User;
use App\Factory\MantenimientoFactory;
use App\Factory\ReclamoFactory;
use App\Factory\SiniestroFactory;
use Zenstruck\Foundry\Story;
use Zenstruck\Foundry\Attribute\AsFixture;

#[AsFixture('reclamo_story')]
final class ReclamoStory extends Story
{
    public function build(): void
    {
        SiniestroFactory::createMany(4);
        MantenimientoFactory::createMany(2);
        ReclamoFactory::createMany(30);

    }
}
