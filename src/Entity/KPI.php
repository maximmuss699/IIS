<?php

namespace App\Entity;

use App\Repository\KPIRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: KPIRepository::class)]
class KPI
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(nullable: true)]
    private ?int $value = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $function = null;

    #[ORM\OneToOne(inversedBy: 'kPI', cascade: ['persist', 'remove'])]
    private ?Parameters $parameter = null;

    #[ORM\ManyToOne(inversedBy: 'KPI')]
    private ?Systems $systems = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(int $id): static
    {
        $this->id = $id;

        return $this;
    }

    public function getValue(): ?int
    {
        return $this->value;
    }

    public function setValue(?int $value): static
    {
        $this->value = $value;

        return $this;
    }

    public function getFunction(): ?string
    {
        return $this->function;
    }

    public function setFunction(?string $function): static
    {
        $this->function = $function;

        return $this;
    }

    public function getParameter(): ?Parameters
    {
        return $this->parameter;
    }

    public function setParameter(?Parameters $parameter): static
    {
        $this->parameter = $parameter;

        return $this;
    }

    public function getSystems(): ?Systems
    {
        return $this->systems;
    }

    public function setSystems(?Systems $systems): static
    {
        $this->systems = $systems;

        return $this;
    }
}
