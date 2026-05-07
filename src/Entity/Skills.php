<?php

namespace App\Entity;

use App\Repository\SkillsRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: SkillsRepository::class)]
#[ORM\Table(name: 'skills')]
class Skills
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(name: 'id_skill')]
    private ?int $id = null; // المعرف الأساسي للسطر

    #[ORM\Column(type: 'integer')]
    private ?int $idTag = null; // رقم الوسم الموحد

    #[ORM\Column(type: 'string', length: 100)]
    private ?string $tagName = null; // اسم الوسم (مثلاً: PHP, Java, UI/UX)

    // --- ID ---
    public function getId(): ?int { return $this->id; }

    // --- ID Tag ---
    public function getIdTag(): ?int { return $this->idTag; }
    public function setIdTag(int $idTag): self {
        $this->idTag = $idTag;
        return $this;
    }

    // --- Tag Name ---
    public function getTagName(): ?string { return $this->tagName; }
    public function setTagName(string $tagName): self {
        $this->tagName = $tagName;
        return $this;
    }






    // داخل كلاس Skills
    public function __toString(): string
    {
        // أخبر Symfony أن يستخدم اسم الوسم (tagName) عند محاولة تحويل الكائن لنص
        return $this->tagName ?? '';
    }
}