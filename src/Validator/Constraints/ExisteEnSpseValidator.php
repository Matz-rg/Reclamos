<?php

namespace App\Validator\Constraints;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class ExisteEnSpseValidator extends ConstraintValidator
{
    private HttpClientInterface $client;
    private string $baseUrl;

    public function __construct(HttpClientInterface $client, string $baseUrl)
    {
        $this->client  = $client;
        $this->baseUrl = rtrim($baseUrl, '/');
    }

    public function validate($value, Constraint $constraint)
    {
        if (!$constraint instanceof ExisteEnSpse) {
            return;
        }

        if ($value === null || $value === '') {
            return;
        }

        $numero = (string) $value;

        try {
            // Ajustá la ruta cuando estés en la red interna
            $response = $this->client->request(
                'GET',
                $this->baseUrl . '/api/maestros/' . $numero,
                ['timeout' => 5]
            );
            $status = $response->getStatusCode();

            if ($status === 404) {
                $this->context->buildViolation($constraint->message)
                    ->setParameter('{{ value }}', $numero)
                    ->addViolation();
                return;
            }

            if ($status !== 200) {
                $this->context->buildViolation($constraint->apiErrorMessage)
                    ->addViolation();
                return;
            }

            $data = json_decode($response->getContent(false), true);

            if (empty($data)) {
                $this->context->buildViolation($constraint->message)
                    ->setParameter('{{ value }}', $numero)
                    ->addViolation();
            }

        } catch (\Throwable $e) {
            $this->context->buildViolation($constraint->apiErrorMessage)
                ->addViolation();
        }
    }
}
