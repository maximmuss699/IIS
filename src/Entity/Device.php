<?php

namespace App\Entity;

use App\Repository\DeviceRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Uid\Uuid;

#[ORM\Entity(repositoryClass: DeviceRepository::class)]
class Device
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $description = null;

    #[ORM\Column(length: 255)]
    private ?string $user_alias = null;

    #[ORM\OneToOne(cascade: ['persist', 'remove'])]
    #[ORM\JoinColumn(nullable: false)]
    private ?Type $Type = null;

    #[ORM\ManyToOne(inversedBy: 'Devices')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Systems $systems = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(Uuid $id): static
    {
        $this->id = $id;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): static
    {
        $this->description = $description;

        return $this;
    }

    public function getUserAlias(): ?string
    {
        return $this->user_alias;
    }

    public function setUserAlias(string $user_alias): static
    {
        $this->user_alias = $user_alias;

        return $this;
    }

    public function getType(): ?Type
    {
        return $this->Type;
    }

    public function setType(Type $Type): static
    {
        $this->Type = $Type;

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
