<?php

namespace App\Entity;

use App\Repository\UniversityRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: UniversityRepository::class)]
#[ORM\Table(name: 'university')]
class University
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255, unique: true)]
    private string $name;

    /** @var Collection<int, Department> */
    #[ORM\OneToMany(mappedBy: 'university', targetEntity: Department::class, cascade: ['persist', 'remove'], orphanRemoval: true)]
    private Collection $departments;

    /** @var Collection<int, User> */
    #[ORM\OneToMany(mappedBy: 'universityRef', targetEntity: User::class)]
    private Collection $admins;


    public function __construct(string $name = '')
    {
        $this->name        = $name;
        $this->departments = new ArrayCollection();
        $this->admins      = new ArrayCollection();
    }

    // ── Getters / Setters ─────────────────────────────────────────────────────

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): static
    {
        $this->name = $name;
        return $this;
    }

    /** @return Collection<int, Department> */
    public function getDepartments(): Collection
    {
        return $this->departments;
    }

    public function addDepartment(Department $department): static
    {
        if (!$this->departments->contains($department)) {
            $this->departments->add($department);
            $department->setUniversity($this);
        }
        return $this;
    }

    public function removeDepartment(Department $department): static
    {
        if ($this->departments->removeElement($department)) {
            if ($department->getUniversity() === $this) {
                $department->setUniversity(null);
            }
        }
        return $this;
    }

    /** @return Collection<int, User> */
    public function getAdmins(): Collection
    {
        return $this->admins;
    }

    public function __toString(): string
    {
        return $this->name;
    }
}