<?php

namespace App\Entity;

use App\Repository\MantenimientoRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\Collection;
#[ORM\HasLifecycleCallbacks]
#[ORM\Entity(repositoryClass: MantenimientoRepository::class)]
class Mantenimiento
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 50)]
    private ?string $servicio = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $detalle = null;

    #[ORM\Column(nullable: true)]
    private ?\DateTimeImmutable $createdAt = null;

    #[ORM\Column(nullable: true)]
    private ?\DateTimeImmutable $updatedAt = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $createdUser = null;

    #[ORM\OneToMany(mappedBy: 'mantenimiento', targetEntity: Reclamo::class)]
    private Collection $reclamos;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getServicio(): ?string
    {
        return $this->servicio;
    }

    public function setServicio(string $servicio): static
    {
        $this->servicio = $servicio;

        return $this;
    }

    public function getDetalle(): ?string
    {
        return $this->detalle;
    }

    public function setDetalle(?string $detalle): static
    {
        $this->detalle = $detalle;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function setCreatedAt(?\DateTimeImmutable $createdAt): static
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getUpdatedAt(): ?\DateTimeImmutable
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(?\DateTimeImmutable $updatedAt): static
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }
    #[ORM\PrePersist]
    public function setCreatedAtValue(): void
    {
        if ($this->createdAt === null) {
            $this->createdAt = new \DateTimeImmutable();
        }
    }

    #[ORM\PreUpdate]
    public function setUpdatedAtValue(): void
    {
        $this->updatedAt = new \DateTimeImmutable();
    }

    public function getCreatedUser(): ?string
    {
        return $this->createdUser;
    }

    public function setCreatedUser(?string $createdUser): static
    {
        $this->createdUser = $createdUser;

        return $this;
    }
    public function __toString(): string
    {
        return $this->servicio . ' - ' . ($this->detalle ?? 'sin detalle');
    }
}
