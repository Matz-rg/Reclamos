<?php

namespace App\Factory;

use App\Entity\Mantenimiento;
use Zenstruck\Foundry\Persistence\PersistentProxyObjectFactory;

final class MantenimientoFactory extends PersistentProxyObjectFactory
{
    public static function class(): string
    {
        return Mantenimiento::class;
    }

    protected function defaults(): array|callable
    {
        return [
            'servicio' => self::faker()->randomElement([
                'energia',
                'saneamiento',
            ]),
            'detalle' => self::faker()->sentence(12),
            'createdAt' => new \DateTimeImmutable(),
            'updatedAt' => null,
            'createdUser' => self::faker()->name(),
        ];
    }

    protected function initialize(): static
    {
        return $this;
    }
}

