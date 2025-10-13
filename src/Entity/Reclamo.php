<?php

namespace App\Entity;

use App\Repository\ReclamoRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ReclamoRepository::class)]
class Reclamo
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $Servicio = null;

    #[ORM\Column(length: 255)]
    private ?string $numeroCliente = null;

    #[ORM\Column(length: 255)]
    private ?string $numeroMedidor = null;

    #[ORM\Column(length: 255)]
    private ?string $Domicilio = null;

    #[ORM\Column(length: 255)]
    private ?string $Usuario = null;

    #[ORM\Column(length: 255)]
    private ?string $Motivo = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getServicio(): ?string
    {
        return $this->Servicio;
    }

    public function setServicio(string $Servicio): static
    {
        $this->Servicio = $Servicio;

        return $this;
    }

    public function getNumeroCliente(): ?string
    {
        return $this->numeroCliente;
    }

    public function setNumeroCliente(string $numeroCliente): static
    {
        $this->numeroCliente = $numeroCliente;

        return $this;
    }

    public function getNumeroMedidor(): ?string
    {
        return $this->numeroMedidor;
    }

    public function setNumeroMedidor(string $numeroMedidor): static
    {
        $this->numeroMedidor = $numeroMedidor;

        return $this;
    }

    public function getDomicilio(): ?string
    {
        return $this->Domicilio;
    }

    public function setDomicilio(string $Domicilio): static
    {
        $this->Domicilio = $Domicilio;

        return $this;
    }

    public function getUsuario(): ?string
    {
        return $this->Usuario;
    }

    public function setUsuario(string $Usuario): static
    {
        $this->Usuario = $Usuario;

        return $this;
    }

    public function getMotivo(): ?string
    {
        return $this->Motivo;
    }

    public function setMotivo(string $Motivo): static
    {
        $this->Motivo = $Motivo;

        return $this;
    }
}
