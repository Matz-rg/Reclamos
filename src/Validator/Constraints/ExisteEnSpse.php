<?php

namespace App\Validator\Constraints;

use Symfony\Component\Validator\Constraint;

#[\Attribute(\Attribute::TARGET_PROPERTY)]
class ExisteEnSpse extends Constraint
{
    public string $message = 'El número de cliente "{{ value }}" no existe en la base de SPSE.';
    public string $apiErrorMessage = 'No se pudo verificar la existencia del número de cliente en SPSE.';

    public function validatedBy(): string
    {
        return ExisteEnSpseValidator::class;
    }

    public function getTargets(): string
    {
        return self::PROPERTY_CONSTRAINT;
    }
}
