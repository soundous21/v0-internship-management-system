<?php

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;
#[ORM\Entity(repositoryClass: UserRepository::class)]
#[ORM\Table(name: '`user`')]
#[UniqueEntity(fields: ['email'], message: 'هذا البريد الإلكتروني مستخدم بالفعل.')]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 180, unique: true)]
    private ?string $email = null;

    #[ORM\Column(type: 'json')]
    private array $roles = [];

    #[ORM\Column]
    private ?string $password = null;

    #[ORM\Column(length: 100, nullable: true)]
    private ?string $firstName = null;

    #[ORM\Column(length: 100, nullable: true)]
    private ?string $lastName = null;

    #[ORM\Column(length: 150, nullable: true)]
    private ?string $companyName = null;

    // التصحيح هنا: إضافة type: Types::DATETIME لضمان تحويل النص القادم من قاعدة البيانات إلى Object
    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $createdAt = null;

    #[ORM\Column(type: 'json', nullable: true)]
    private ?array $skills = [];

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $githubLink = null;
    public function __construct()
    {
        // استخدام DateTime العادي لضمان التوافق مع XAMPP
        $this->createdAt = new \DateTime();
        $this->roles = [];
    }
    #[ORM\Column(length: 20, nullable: true)]
    private ?string $phone = null;

    #[ORM\Column(length: 100, nullable: true)]
    private ?string $wilaya = null;

    #[ORM\Column(length: 100, nullable: true)]
    private ?string $specialty = null;

    #[ORM\Column(length: 50, nullable: true)]
    private ?string $level = null;

    #[ORM\Column(type: 'text', nullable: true)]
    private ?string $bio = null;

    // 1. إضافة مجال العمل للشركة
    #[ORM\Column(length: 100, nullable: true)]
    private ?string $industry = null;

// 2. إضافة الموقع الإلكتروني
    #[ORM\Column(length: 255, nullable: true)]
    private ?string $website = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $logo = null;

    // ── وظائف نظام الحماية ──
    public function getUserIdentifier(): string { return (string) $this->email; }

    public function getRoles(): array {
        $roles = $this->roles;
        $roles[] = 'ROLE_USER';
        return array_unique($roles);
    }

    public function setRoles(array $roles): static { $this->roles = $roles; return $this; }
    public function getPassword(): ?string { return $this->password; }
    public function setPassword(string $password): static { $this->password = $password; return $this; }
    public function eraseCredentials(): void {}




    public function getIndustry(): ?string { return $this->industry; }
    public function setIndustry(?string $industry): static { $this->industry = $industry; return $this; }

    public function getWebsite(): ?string { return $this->website; }
    public function setWebsite(?string $website): static { $this->website = $website; return $this; }

    public function getSkills(): array
    {
        return $this->skills ?? [];
    }

    public function setSkills(?array $skills): static
    {
        $this->skills = $skills;
        return $this;
    }

    public function getGithubLink(): ?string { return $this->githubLink; }
    public function setGithubLink(?string $githubLink): static {
        $this->githubLink = $githubLink;
        return $this;
    }

    // ── Getters & Setters ──
    public function getId(): ?int { return $this->id; }

    public function getEmail(): ?string { return $this->email; }
    public function setEmail(string $email): static { $this->email = $email; return $this; }

    public function getFirstName(): ?string { return $this->firstName; }
    public function setFirstName(?string $firstName): static { $this->firstName = $firstName; return $this; }

    public function getLastName(): ?string { return $this->lastName; }
    public function setLastName(?string $lastName): static { $this->lastName = $lastName; return $this; }

    public function getCompanyName(): ?string { return $this->companyName; }
    public function setCompanyName(?string $companyName): static { $this->companyName = $companyName; return $this; }

    public function getCreatedAt(): ?\DateTimeInterface { return $this->createdAt; }

    // إضافة Setter للتاريخ لتمكين Doctrine من حقن البيانات بشكل صحيح
    public function setCreatedAt(\DateTimeInterface $createdAt): static
    {
        $this->createdAt = $createdAt;
        return $this;
    }

    public function getFullName(): string { return $this->firstName . ' ' . $this->lastName; }







    // ... الحقول السابقة في ملف User.php

    public function getPhone(): ?string
    {
        return $this->phone;
    }

    public function setPhone(?string $phone): static
    {
        $this->phone = $phone;
        return $this;
    }

    public function getWilaya(): ?string
    {
        return $this->wilaya;
    }

    public function setWilaya(?string $wilaya): static
    {
        $this->wilaya = $wilaya;
        return $this;
    }

    public function getSpecialty(): ?string
    {
        return $this->specialty;
    }

    public function setSpecialty(?string $specialty): static
    {
        $this->specialty = $specialty;
        return $this;
    }

    public function getLevel(): ?string
    {
        return $this->level;
    }

    public function setLevel(?string $level): static
    {
        $this->level = $level;
        return $this;
    }

    public function getBio(): ?string
    {
        return $this->bio;
    }

    public function setBio(?string $bio): static
    {
        $this->bio = $bio;
        return $this;
    }

    public function getLogo(): ?string
    {
        return $this->logo;
    }

    public function setLogo(?string $logo): static
    {
        $this->logo = $logo;

        return $this;
    }
}

// ... الحقول السابقة في ملف User.php





