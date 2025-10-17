<?php

namespace App\Factory;

use App\Entity\Reclamo;
use Zenstruck\Foundry\Persistence\PersistentProxyObjectFactory;

/**
 * @extends PersistentProxyObjectFactory<Reclamo>
 */
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
                'Agua potable',
                'Cloacas',
                'Luz eléctrica',
                'Gas natural',
                'Recolección residuos',
                'Alumbrado público'
            ]),
            'Usuario' => self::faker()->name(),
            'numeroCliente' => 'CLI-' . self::faker()->numerify('######'),
            'numeroMedidor' => 'MED-' . self::faker()->numerify('########'),
            'Detalle' => self::faker()->sentence(),
        ];
    }

    protected function initialize(): static
    {
        return $this
            // ->afterInstantiate(function(Reclamo $reclamo): void {})
            ;
    }
}
