<?php

namespace App\Story;

use App\Factory\ReclamoFactory;
use Zenstruck\Foundry\Story;
use Zenstruck\Foundry\Attribute\AsFixture;

#[AsFixture('reclamo_story')] // ← Añade un nombre al fixture
final class ReclamoStory extends Story
{
    public function build(): void
    {
        // Crear 15 reclamos de prueba con datos realistas
        ReclamoFactory::createMany(100);
    }
}
