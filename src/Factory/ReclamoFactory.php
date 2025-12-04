<?php

namespace App\Factory;

use App\Entity\Reclamo;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Zenstruck\Foundry\Persistence\PersistentProxyObjectFactory;

final class ReclamoFactory extends PersistentProxyObjectFactory
{
    private static array $reclamos = [];
    private HttpClientInterface $httpClient;

    public function __construct(HttpClientInterface $httpClient)
    {
        $this->httpClient = $httpClient;
    }

    public static function class(): string
    {
        return Reclamo::class;
    }

    protected function defaults(): array|callable
    {
        $response = $this->httpClient->request(method: 'GET', url: 'https://apivt.spse.app/api/maestros?page=100');
        $content = $response->toArray();
        $arrClient = [];
        foreach ($content['hydra:member'] as $cliente) {
            $arrClient[] = $cliente['nrocliente'];
        }
        //dd($content["hydra:member"]);
        //dd(array_column($content, 'nrocliente'));

        //Este if es para eliminar el problema del UNIQUE al momento de generar el factory
        if (empty(self::$reclamos)) {
            $response = $this->httpClient->request(
                'GET',
                'https://apivt.spse.app/api/maestros?page=100'
            );

            $content = $response->toArray();

            foreach ($content['hydra:member'] as $cliente) {
                self::$reclamos[] = (string) $cliente['nrocliente'];
            }

            // Eliminamos duplicados por seguridad
            self::$reclamos = array_values(array_unique(self::$reclamos));

            // Mezclamos el orden
            shuffle(self::$reclamos);
        }

        // Sacamos uno y no se vuelve a usar
        $numeroCliente = array_shift(self::$reclamos);
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
            'numeroCliente' => $numeroCliente,
            'numeroMedidor' => 'MED-' . self::faker()->numerify('########'),
            'Detalle' => self::faker()->sentence(),
            'Estado' => self::faker()->randomElement(['Creado', 'En Guardia']),
            'siniestro' => \App\Factory\SiniestroFactory::randomOrCreate(),
        ];
    }

    protected function initialize(): static
    {

        return $this;
    }
}

