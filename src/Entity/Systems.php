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

    #[ORM\ManyToMany(targetEntity: User::class, mappedBy: 'Systems')]
    private Collection $users;

    #[ORM\ManyToOne(inversedBy: 'CreatedSystems')]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $userOwner = null;

    public function __construct()
    {
        $this->Devices = new ArrayCollection();
        $this->users = new ArrayCollection();
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

    /**
     * @return Collection<int, User>
     */
    public function getUsers(): Collection
    {
        return $this->users;
    }

    public function addUser(User $user): static
    {
        if (!$this->users->contains($user)) {
            $this->users->add($user);
            $user->addSystem($this);
        }

        return $this;
    }

    public function removeUser(User $user): static
    {
        if ($this->users->removeElement($user)) {
            $user->removeSystem($this);
        }

        return $this;
    }

    public function getUserOwner(): ?User
    {
        return $this->userOwner;
    }

    public function setUserOwner(?User $userOwner): static
    {
        $this->userOwner = $userOwner;

        return $this;
    }
}
