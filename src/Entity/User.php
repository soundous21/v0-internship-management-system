<?php

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

#[ORM\Entity(repositoryClass: UserRepository::class)]
#[ORM\Table(name: '`user`')]
#[UniqueEntity(fields: ['email'], message: 'هذا البريد الإلكتروني مستخدم بالفعل.')]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    // =========================================================================
    // الحقول الأساسية
    // =========================================================================

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

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $createdAt = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $githubLink = null;

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

    #[ORM\Column(length: 100, nullable: true)]
    private ?string $industry = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $website = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $logo = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $university = null;

    #[ORM\Column(length: 150, nullable: true)]
    private ?string $universityName = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $portfolioLink = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $profilePicture = null;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private ?string $latitude = null;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private ?string $longitude = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $verificationFile = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $stampFilename = null;

    // =========================================================================
    // ★ العلاقة 1: طالب ───ManyToOne──▶ أدمين جامعته
    //   الطالب يعرف أدمينه الواحد عبر هذا الحقل.
    //   OWNING SIDE على جانب الطالب.
    // =========================================================================

    #[ORM\ManyToOne(targetEntity: self::class, inversedBy: 'students')]
    #[ORM\JoinColumn(name: 'university_id', referencedColumnName: 'id', nullable: true, onDelete: 'SET NULL')]
    private ?User $universityEntity = null;

    // =========================================================================
    // ★ العلاقة 1 (عكسية): أدمين ───OneToMany──▶ طلابه
    //   الأدمين يرى كل طلابه دفعةً واحدة.
    //   INVERSE SIDE.
    // =========================================================================

    #[ORM\OneToMany(mappedBy: 'universityEntity', targetEntity: self::class)]
    private Collection $students;

    // =========================================================================
    // ★ العلاقة 2: شركة ◄──ManyToMany──▶ أدمين
    //
    //   جدول الربط:  company_university
    //     company_id    → id الشركة (User بـ ROLE_COMPANY)
    //     university_id → id الأدمين (User بـ ROLE_ADMIN)
    //
    //   تُضاف السجلات في هذا الجدول تلقائياً عند قبول VerificationRequest.
    //   OWNING SIDE على جانب الشركة (partnerUniversities).
    // =========================================================================

    /** الشركة: قائمة جامعاتها الشريكة */
    #[ORM\ManyToMany(targetEntity: self::class, inversedBy: 'partnerCompanies')]
    #[ORM\JoinTable(name: 'company_university')]
    #[ORM\JoinColumn(name: 'company_id', referencedColumnName: 'id')]
    #[ORM\InverseJoinColumn(name: 'university_id', referencedColumnName: 'id')]
    private Collection $partnerUniversities;

    /** الأدمين: قائمة شركاته (الجهة العكسية) */
    #[ORM\ManyToMany(targetEntity: self::class, mappedBy: 'partnerUniversities')]
    private Collection $partnerCompanies;

    // =========================================================================
    // المهارات (موجودة مسبقاً)
    // =========================================================================

    #[ORM\ManyToMany(targetEntity: Skills::class)]
    #[ORM\JoinTable(name: 'user_skills')]
    #[ORM\JoinColumn(name: 'user_id', referencedColumnName: 'id')]
    #[ORM\InverseJoinColumn(name: 'skill_id', referencedColumnName: 'id_skill')]
    private Collection $skills;

    // =========================================================================
    // Constructor
    // =========================================================================

    public function __construct()
    {
        $this->createdAt           = new \DateTime();
        $this->roles               = [];
        $this->skills              = new ArrayCollection();
        $this->students            = new ArrayCollection();   // ← جديد
        $this->partnerUniversities = new ArrayCollection();   // ← جديد
        $this->partnerCompanies    = new ArrayCollection();   // ← جديد
    }

    // =========================================================================
    // Getters / Setters — الحقول الأساسية
    // =========================================================================

    public function getId(): ?int { return $this->id; }

    public function getEmail(): ?string { return $this->email; }
    public function setEmail(string $email): static { $this->email = $email; return $this; }

    public function getRoles(): array
    {
        $roles = $this->roles;
        $roles[] = 'ROLE_USER';
        return array_unique($roles);
    }
    public function setRoles(array $roles): static { $this->roles = $roles; return $this; }

    public function getPassword(): ?string { return $this->password; }
    public function setPassword(string $password): static { $this->password = $password; return $this; }

    public function eraseCredentials(): void {}
    public function getUserIdentifier(): string { return (string) $this->email; }

    public function getFirstName(): ?string { return $this->firstName; }
    public function setFirstName(?string $v): static { $this->firstName = $v; return $this; }

    public function getLastName(): ?string { return $this->lastName; }
    public function setLastName(?string $v): static { $this->lastName = $v; return $this; }

    public function getFullName(): string { return $this->firstName . ' ' . $this->lastName; }

    public function getCompanyName(): ?string { return $this->companyName; }
    public function setCompanyName(?string $v): static { $this->companyName = $v; return $this; }

    public function getCreatedAt(): ?\DateTimeInterface { return $this->createdAt; }
    public function setCreatedAt(\DateTimeInterface $v): static { $this->createdAt = $v; return $this; }

    public function getPhone(): ?string { return $this->phone; }
    public function setPhone(?string $v): static { $this->phone = $v; return $this; }

    public function getWilaya(): ?string { return $this->wilaya; }
    public function setWilaya(?string $v): static { $this->wilaya = $v; return $this; }

    public function getSpecialty(): ?string { return $this->specialty; }
    public function setSpecialty(?string $v): static { $this->specialty = $v; return $this; }

    public function getLevel(): ?string { return $this->level; }
    public function setLevel(?string $v): static { $this->level = $v; return $this; }

    public function getBio(): ?string { return $this->bio; }
    public function setBio(?string $v): static { $this->bio = $v; return $this; }

    public function getIndustry(): ?string { return $this->industry; }
    public function setIndustry(?string $v): static { $this->industry = $v; return $this; }

    public function getWebsite(): ?string { return $this->website; }
    public function setWebsite(?string $v): static { $this->website = $v; return $this; }

    public function getLogo(): ?string { return $this->logo; }
    public function setLogo(?string $v): static { $this->logo = $v; return $this; }

    public function getUniversity(): ?string { return $this->university; }
    public function setUniversity(?string $v): static { $this->university = $v; return $this; }

    public function getUniversityName(): ?string { return $this->universityName; }
    public function setUniversityName(?string $v): static { $this->universityName = $v; return $this; }

    public function getPortfolioLink(): ?string { return $this->portfolioLink; }
    public function setPortfolioLink(?string $v): static { $this->portfolioLink = $v; return $this; }

    public function getProfilePicture(): ?string { return $this->profilePicture; }
    public function setProfilePicture(?string $v): static { $this->profilePicture = $v; return $this; }

    public function getLatitude(): ?string { return $this->latitude; }
    public function setLatitude(?string $v): static { $this->latitude = $v; return $this; }

    public function getLongitude(): ?string { return $this->longitude; }
    public function setLongitude(?string $v): static { $this->longitude = $v; return $this; }

    public function getGithubLink(): ?string { return $this->githubLink; }
    public function setGithubLink(?string $v): static { $this->githubLink = $v; return $this; }

    public function getVerificationFile(): ?string { return $this->verificationFile; }
    public function setVerificationFile(?string $v): static { $this->verificationFile = $v; return $this; }

    public function getStampFilename(): ?string { return $this->stampFilename; }
    public function setStampFilename(?string $v): static { $this->stampFilename = $v; return $this; }

    // =========================================================================
    // Getters / Setters — العلاقات
    // =========================================================================

    // ── طالب ↔ أدمين ─────────────────────────────────────────────────────────

    public function getUniversityEntity(): ?User { return $this->universityEntity; }
    public function setUniversityEntity(?User $university): static
    {
        $this->universityEntity = $university;
        return $this;
    }

    /** جلب كل طلاب هذا الأدمين */
    public function getStudents(): Collection { return $this->students; }

    // ── شركة ↔ أدمين ─────────────────────────────────────────────────────────

    /** جامعات هذه الشركة الشريكة (للشركة) */
    public function getPartnerUniversities(): Collection { return $this->partnerUniversities; }

    public function addPartnerUniversity(User $university): static
    {
        if (!$this->partnerUniversities->contains($university)) {
            $this->partnerUniversities->add($university);
        }
        return $this;
    }

    public function removePartnerUniversity(User $university): static
    {
        $this->partnerUniversities->removeElement($university);
        return $this;
    }

    /** شركات هذا الأدمين الشريكة (للأدمين) */
    public function getPartnerCompanies(): Collection { return $this->partnerCompanies; }

    /** هل الشركة شريكة مع هذا الأدمين؟ */
    public function isPartnerWith(User $university): bool
    {
        return $this->partnerUniversities->contains($university);
    }

    // ── المهارات ──────────────────────────────────────────────────────────────

    public function getSkills(): Collection { return $this->skills; }

    public function addSkill(Skills $skill): static
    {
        if (!$this->skills->contains($skill)) {
            $this->skills->add($skill);
        }
        return $this;
    }

    public function removeSkill(Skills $skill): static
    {
        $this->skills->removeElement($skill);
        return $this;
    }
}