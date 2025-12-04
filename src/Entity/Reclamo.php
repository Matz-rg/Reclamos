<?php

namespace App\Entity;


use ApiPlatform\Metadata\Post;
use App\Repository\ReclamoRepository;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\DBAL\Types\Types;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Doctrine\Orm\Filter\SearchFilter;
use ApiPlatform\Doctrine\Orm\Filter\DateFilter;
use ApiPlatform\Metadata\ApiFilter;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Context\ExecutionContextInterface;
use App\Validator\Constraints as AppAssert;
use Gedmo\Mapping\Annotation as Gedmo;




#[ORM\Entity(repositoryClass: ReclamoRepository::class)]
#[ORM\Table(
    name: 'reclamo',
    indexes: [ new ORM\Index(name: 'idx_numero_cliente', columns: ['numero_cliente']) ]
)]
#[ApiResource(
         operations: [
             new Get(),
             new GetCollection(),
         ]
     )]
#[ApiFilter(SearchFilter::class, properties: [
    'estado' => 'exact',
    'descripcion' => 'partial',
])]
#[ApiFilter(DateFilter::class, properties: [
    'fechaCreacion'
])]
#[ApiResource(
    operations: [
        new Get(),
        new GetCollection(),
    ]
)]
#[Assert\Callback('validate')]
class Reclamo
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $Servicio = null;

    #[ORM\Column(type: 'bigint', unique: true)]
    #[Assert\NotBlank]
    #[AppAssert\ExisteEnSpse]
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
    private ?string $estado = 'Creado';

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $fechaCreacion = null;

    #[Gedmo\Timestampable(on: 'change', field: 'estado', value: 'Atendiendo')]
    #[ORM\Column(nullable: true)]
    private ?\DateTime $fechaDeVisualizacion = null;

    #[ORM\Column(nullable: true)]
    private ?\DateTime $fechaCierre = null;
    #[ORM\Column(nullable: true)]
    private ?String $userCierre = null;

    #[ORM\Column(nullable: true)]
    private ?String $causa = null;
    #[ORM\ManyToOne(targetEntity: Siniestro::class)]
    #[ORM\JoinColumn(nullable: true)]
    private ?Siniestro $siniestro = null;

    public function __construct()
    {
        $this->fechaCreacion = new \DateTime();
    }

    public function getSiniestro(): ?Siniestro
    {
        return $this->siniestro;
    }

    public function setSiniestro(?Siniestro $siniestro): void
    {
        $this->siniestro = $siniestro;
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

    public function setNumeroCliente(string $numeroCliente): self
    {
        $this->numeroCliente = $numeroCliente;
        return $this;
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

    public function validate(ExecutionContextInterface $context)
    {
        if ($this->numeroCliente < 310001000000 || $this->numeroCliente > 540001400000) {
            $context->buildViolation('Número fuera del rango permitido.')
                ->atPath('numeroCliente')
                ->addViolation();
        }
    }

    public function getFechaDeVisualizacion(): ?\DateTime
    {
        return $this->fechaDeVisualizacion;
    }

    public function setFechaDeVisualizacion(?\DateTime $fechaDeVisualizacion): static
    {
        $this->fechaDeVisualizacion = $fechaDeVisualizacion;

        return $this;
    }

    public function getFechaCierre(): ?\DateTime
    {
        return $this->fechaCierre;
    }

    public function setFechaCierre(?\DateTime $fechaCierre): void
    {
        $this->fechaCierre = $fechaCierre;
    }

    public function getUserCierre(): ?string
    {
        return $this->userCierre;
    }

    public function setUserCierre(?string $userCierre): void
    {
        $this->userCierre = $userCierre;
    }

    public function getCausa(): ?string
    {
        return $this->causa;
    }

    public function setCausa(?string $causa): void
    {
        $this->causa = $causa;
    }




}


