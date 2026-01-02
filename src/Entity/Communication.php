<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'communication')]
class Communication
{
    #[ORM\Id, ORM\GeneratedValue, ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $title = null;

    #[ORM\Column(type: 'text')]
    private ?string $abstract = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $keywords = null;

    #[ORM\Column(length: 50)]
    private ?string $type = null;

    #[ORM\Column(length: 255)]
    private ?string $pdf_filename = null;

    #[ORM\Column(length: 50)]
    private ?string $status = 'En attente';

    #[ORM\ManyToOne(targetEntity: User::class)]
    #[ORM\JoinColumn(name: 'author_id', referencedColumnName: 'id', onDelete: 'CASCADE')]
    private ?User $author = null;

    #[ORM\ManyToOne(targetEntity: ScientificEvent::class)]
    #[ORM\JoinColumn(name: 'event_id', referencedColumnName: 'id', onDelete: 'CASCADE')]
    private ?ScientificEvent $event = null;

    // Getters & Setters ...
    // src/Entity/Communication.php
// src/Entity/Communication.php

// ... الكود الحالي ...

    public function getStatus(): ?string
    {
        return $this->status;
    }

    public function setStatus(string $status): self
    {
        $this->status = $status;
        return $this;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitle(): ?string
    {
        return $this->title;

} // القوس الأخير للكلاس

}