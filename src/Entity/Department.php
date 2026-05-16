<?php

namespace App\Entity;

use App\Repository\DepartmentRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: DepartmentRepository::class)]
class Department
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $name = null;

    // الربط مع الجامعة (كل قسم ينتمي لجامعة واحدة)
    #[ORM\ManyToOne(targetEntity: University::class, inversedBy: 'departments')]
    #[ORM\JoinColumn(nullable: false)]
    private ?University $university = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;
        return $this;
    }

    public function getUniversity(): ?University
    {
        return $this->university;
    }

    public function setUniversity(?University $university): self
    {
        $this->university = $university;
        return $this;
    }

    public function __toString(): string
    {
        return $this->name;
    }
}