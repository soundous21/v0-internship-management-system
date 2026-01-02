<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'scientific_event')]
class ScientificEvent
{
    #[ORM\Id, ORM\GeneratedValue, ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $title = null;

    #[ORM\Column(type: 'text')]
    private ?string $description = null;

    #[ORM\Column(length: 100)]
    private ?string $type = null;

    #[ORM\Column(type: 'datetime')]
    private ?\DateTimeInterface $start_date = null;

    #[ORM\Column(type: 'datetime')]
    private ?\DateTimeInterface $end_date = null;

    #[ORM\Column(length: 255)]
    private ?string $location = null;

    #[ORM\Column(length: 255)]
    private ?string $theme = null;

    #[ORM\ManyToOne(targetEntity: User::class)]
    #[ORM\JoinColumn(name: 'organizer_id', referencedColumnName: 'id', onDelete: 'SET NULL')]
    private ?User $organizer = null;

    // Getters & Setters ...
}