<?php

namespace App\Entity;

use App\Repository\ReclamoRepository;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\DBAL\Types\Types;

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

    #[ORM\Column(name: 'detalle', type: Types::TEXT, nullable: true)]
    private ?string $Detalle = null;

    #[ORM\Column(length: 255)]
    private ?string $estado = 'Pendiente';

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $fechaCreacion = null;

    public function __construct()
    {
        $this->fechaCreacion = new \DateTime();
    }

    public function getServicio(): ?string
    {
        return $this->Servicio;
    }

    public function setServicio(?string $Servicio): void
    {
        $this->Servicio = $Servicio;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(?int $id): void
    {
        $this->id = $id;
    }

    public function getNumeroCliente(): ?string
    {
        return $this->numeroCliente;
    }

    public function setNumeroCliente(?string $numeroCliente): void
    {
        $this->numeroCliente = $numeroCliente;
    }

    public function getNumeroMedidor(): ?string
    {
        return $this->numeroMedidor;
    }

    public function setNumeroMedidor(?string $numeroMedidor): void
    {
        $this->numeroMedidor = $numeroMedidor;
    }

    public function getDomicilio(): ?string
    {
        return $this->Domicilio;
    }

    public function setDomicilio(?string $Domicilio): void
    {
        $this->Domicilio = $Domicilio;
    }

    public function getUsuario(): ?string
    {
        return $this->Usuario;
    }

    public function setUsuario(?string $Usuario): void
    {
        $this->Usuario = $Usuario;
    }

    public function getMotivo(): ?string
    {
        return $this->Motivo;
    }

    public function setMotivo(?string $Motivo): void
    {
        $this->Motivo = $Motivo;
    }


    public function getDetalle(): ?string
    {
        return $this->Detalle;
    }

    public function setDetalle(?string $Detalle): static
    {
        $this->Detalle = $Detalle;

        return $this;
    }

    public function getEstado(): ?string
    {
        return $this->estado;
    }

    public function setEstado(string $estado): static
    {
        $this->estado = $estado;

        return $this;
    }

    public function getFechaCreacion(): ?\DateTimeInterface
    {
        return $this->fechaCreacion;
    }

    public function setFechaCreacion(?\DateTimeInterface $fechaCreacion): static
    {
        $this->fechaCreacion = $fechaCreacion;
        return $this;
    }
}
