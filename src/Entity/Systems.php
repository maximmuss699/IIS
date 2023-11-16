<?php

namespace App\Entity;

use App\Repository\SystemsRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Ramsey\Uuid\Doctrine\UuidGenerator;
use Ramsey\Uuid\UuidInterface;
#[ORM\Entity(repositoryClass: SystemsRepository::class)]
class Systems
{

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $Name = null;

    #[ORM\OneToMany(mappedBy: 'systems', targetEntity: Device::class, orphanRemoval: true)]
    private Collection $Devices;

    public function __construct()
    {
        $this->Devices = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->Name;
    }

    public function setName(string $Name): static
    {
        $this->Name = $Name;

        return $this;
    }

    /**
     * @return Collection<int, Device>
     */
    public function getDevices(): Collection
    {
        return $this->Devices;
    }

    public function addDevice(Device $device): static
    {
        if (!$this->Devices->contains($device)) {
            $this->Devices->add($device);
            $device->setSystems($this);
        }

        return $this;
    }

    public function removeDevice(Device $device): static
    {
        if ($this->Devices->removeElement($device)) {
            // set the owning side to null (unless already changed)
            if ($device->getSystems() === $this) {
                $device->setSystems(null);
            }
        }

        return $this;
    }
}
