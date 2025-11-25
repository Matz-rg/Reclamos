<?php

namespace App\Factory;

use App\Entity\Reclamo;
use Zenstruck\Foundry\Persistence\PersistentProxyObjectFactory;

final class ReclamoFactory extends PersistentProxyObjectFactory
{
    public function __construct()
    {
    }

    public static function class(): string
    {
        return Reclamo::class;
    }

    protected function defaults(): array|callable
    {
        return [
            'Domicilio' => self::faker()->address(),
            'Motivo' => self::faker()->randomElement([
                'Corte de servicio',
                'Facturación incorrecta',
                'Problema técnico',
                'Falta de suministro',
                'Lectura de medidor errónea',
                'Consulta general',
                'Reclamo por demora',
                'Problema de presión',
                'Fuga reportada',
                'Instalación defectuosa'
            ]),
            'Servicio' => self::faker()->randomElement([
                'Energia',
                'Agua',
                'Cloaca'
            ]),
            'Usuario' => self::faker()->name(),
            'numeroCliente' => (string) self::faker()->numberBetween(310001000000, 540001400000),
            'numeroMedidor' => 'MED-' . self::faker()->numerify('########'),
            'Detalle' => self::faker()->sentence(),
            'Estado' => self::faker()->randomElement(['Creado', 'En Guardia', 'Atendiendo', 'Impreso']),
        ];
    }

    protected function initialize(): static
    {
        return $this;
    }
}

