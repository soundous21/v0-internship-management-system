<?php
namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'workshop')]
class Workshop
{
    #[ORM\Id, ORM\GeneratedValue, ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $title = null;

    // تغيير المسمى ليتطابق مع جدولك اليدوي ويمنع الحذف
    #[ORM\Column(length: 255, nullable: true)]
    private ?string $instructor = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $category = null;

    #[ORM\Column(length: 100, nullable: true)]
    private ?string $workshop_date = null;

    #[ORM\Column(length: 100, nullable: true)]
    private ?string $workshop_time = null;

    #[ORM\Column(nullable: true)]
    private ?int $max_participants = null;

    #[ORM\ManyToOne(targetEntity: ScientificEvent::class)]
    #[ORM\JoinColumn(name: 'event_id', referencedColumnName: 'id', onDelete: 'CASCADE')]
    private ?ScientificEvent $event = null;

    // Getters & Setters...
}