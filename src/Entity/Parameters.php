<?php

namespace App\Entity;

use App\Repository\ParametersRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ParametersRepository::class)]
class Parameters
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $name = null;

    #[ORM\Column(type: Types::JSON)]
    private array $values = [];

    #[ORM\ManyToOne(inversedBy: 'parameters')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Type $type = null;

    #[ORM\OneToOne(mappedBy: 'parameter', cascade: ['persist', 'remove'])]
    private ?KPI $kPI = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): static
    {
        $this->name = $name;

        return $this;
    }

    public function getValues(): array
    {
        return $this->values;
    }

    public function setValues(array $values): static
    {
        $this->values = $values;

        return $this;
    }

    public function getType(): ?Type
    {
        return $this->type;
    }

    public function setType(?Type $type): static
    {
        $this->type = $type;

        return $this;
    }

    public function getKPI(): ?KPI
    {
        return $this->kPI;
    }

    public function setKPI(?KPI $kPI): static
    {
        // unset the owning side of the relation if necessary
        if ($kPI === null && $this->kPI !== null) {
            $this->kPI->setParameter(null);
        }

        // set the owning side of the relation if necessary
        if ($kPI !== null && $kPI->getParameter() !== $this) {
            $kPI->setParameter($this);
        }

        $this->kPI = $kPI;

        return $this;
    }
}
