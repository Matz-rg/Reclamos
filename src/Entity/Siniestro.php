<?php

namespace App\Entity;

use App\Repository\SiniestroRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
#[ORM\HasLifecycleCallbacks]
#[ORM\Entity(repositoryClass: SiniestroRepository::class)]
class Siniestro
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $servicio = null;

    #[ORM\Column(type: Types::TEXT)]
    private ?string $causa = null;

    #[ORM\Column(nullable: true)]
    private ?\DateTimeImmutable $createdAt = null;

    #[ORM\Column(nullable: true)]
    private ?\DateTimeImmutable $updatedAt = null;

    #[ORM\Column(length: 255)]
    private ?string $createdUser = null;
    #[ORM\OneToMany(mappedBy: 'siniestro', targetEntity: Reclamo::class)]
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

    public function getCausa(): ?string
    {
        return $this->causa;
    }

    public function setCausa(string $causa): static
    {
        $this->causa = $causa;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeImmutable $createdAt): static
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getUpdatedAt(): ?\DateTimeImmutable
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(?\DateTimeImmutable $updatedAt): void
    {
        $this->updatedAt = $updatedAt;
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

    public function setCreatedUser(?string $createdUser): void
    {
        $this->createdUser = $createdUser;
    }

    public function __construct()
    {
        $this->reclamos = new ArrayCollection();
    }
    public function getReclamos(): Collection
    {
        return $this->reclamos;
    }

    public function setReclamos(Collection $reclamos): void
    {
        $this->reclamos = $reclamos;
    }
    public function addReclamo(Reclamo $reclamo): static
    {
        if (!$this->reclamos->contains($reclamo)) {
            $this->reclamos->add($reclamo);
            $reclamo->setSiniestro($this);
        }

        return $this;
    }

    public function removeReclamo(Reclamo $reclamo): static
    {
        if ($this->reclamos->removeElement($reclamo)) {
            if ($reclamo->getSiniestro() === $this) {
                $reclamo->setSiniestro(null);
            }
        }

        return $this;
    }
    public function __toString(): string
    {
        return sprintf(
            'Siniestro #%d - %s',
            $this->id,
            $this->servicio
        );
    }


}
