<?php

namespace App\Factory;

use App\Entity\Siniestro;
use App\Repository\SiniestroRepository;
use Doctrine\ORM\EntityRepository;
use Zenstruck\Foundry\Persistence\PersistentProxyObjectFactory;
use Zenstruck\Foundry\Persistence\Proxy;
use Zenstruck\Foundry\Persistence\ProxyRepositoryDecorator;

/**
 * @extends PersistentProxyObjectFactory<Siniestro>
 */
final class SiniestroFactory extends PersistentProxyObjectFactory{
    /**
     * @see https://symfony.com/bundles/ZenstruckFoundryBundle/current/index.html#factories-as-services
     *
     * @todo inject services if required
     */
    public function __construct()
    {
    }

    public static function class(): string
    {
        return Siniestro::class;
    }

    protected function defaults(): array|callable    {
        return [
            'servicio' => self::faker()->randomElement([
                'energia',
                'saneamiento',
            ]),
            'causa' => self::faker()->sentence(8),
            'createdAt' => new \DateTimeImmutable(),
            'updatedAt' => null,
            'createdUser' => self::faker()->name(),
        ];
    }

    protected function initialize(): static
    {
        return $this
            // ->afterInstantiate(function(Siniestro $siniestro): void {})
        ;
    }
}
