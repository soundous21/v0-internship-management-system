<?php

namespace App\Entity;

use App\Repository\VerificationRequestRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: VerificationRequestRepository::class)]
#[ORM\Table(name: 'verification_request')]
class VerificationRequest
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    // الشركة التي أرسلت الطلب
    #[ORM\ManyToOne(targetEntity: User::class)]
    #[ORM\JoinColumn(nullable: false, onDelete: 'CASCADE')]
    private ?User $company = null;

    // الجامعة المستهدفة
    #[ORM\ManyToOne(targetEntity: User::class)]
    #[ORM\JoinColumn(nullable: false, onDelete: 'CASCADE')]
    private ?User $university = null;

    #[ORM\Column(type: 'datetime')]
    private ?\DateTimeInterface $createdAt = null;

    // القيم الممكنة: Pending, Accepted, Rejected
    #[ORM\Column(length: 50)]
    private string $status = 'Pending';

    public function __construct()
    {
        $this->createdAt = new \DateTime();
    }




    // src/Entity/VerificationRequest.php

// ... الكود الموجود مسبقاً

    #[ORM\Column(type: 'text', nullable: true)] // جعلناه text ليتحمل رسائل طويلة و nullable لأن الطلبات الجديدة لا تملك سبباً
    private ?string $rejectionReason = null;

// أضف الدوال التالية في نهاية الكلاس قبل القوس الأخير:

    public function getRejectionReason(): ?string
    {
        return $this->rejectionReason;
    }

    public function setRejectionReason(?string $rejectionReason): static
    {
        $this->rejectionReason = $rejectionReason;
        return $this;
    }
    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCompany(): ?User
    {
        return $this->company;
    }

    public function setCompany(?User $company): static
    {
        $this->company = $company;
        return $this;
    }

    public function getUniversity(): ?User
    {
        return $this->university;
    }

    public function setUniversity(?User $university): static
    {
        $this->university = $university;
        return $this;
    }

    public function getCreatedAt(): ?\DateTimeInterface
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeInterface $createdAt): static
    {
        $this->createdAt = $createdAt;
        return $this;
    }

    public function getStatus(): string
    {
        return $this->status;
    }

    public function setStatus(string $status): static
    {
        $this->status = $status;
        return $this;
    }
}