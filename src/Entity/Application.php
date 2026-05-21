<?php

namespace App\Entity;

use App\Repository\ApplicationRepository;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\DBAL\Types\Types; // أضيفي هذا السطر في أعلى الملف

#[ORM\Entity(repositoryClass: ApplicationRepository::class)]
#[ORM\Table(name: 'applications')]
class Application
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    // الربط مع العرض (Offer)
    #[ORM\ManyToOne(targetEntity: Offers::class)]
    #[ORM\JoinColumn(nullable: false, onDelete: "CASCADE")]
    private ?Offers $offer = null;

    // الربط مع الطالب (المستخدم الذي يتقدم للطلب)
    #[ORM\ManyToOne(targetEntity: User::class)]
    #[ORM\JoinColumn(nullable: false, onDelete: "CASCADE")]
    private ?User $student = null;

    #[ORM\Column(type: 'datetime')]
    private ?\DateTimeInterface $createdAt = null;

    #[ORM\Column(length: 50)]
    private ?string $status = 'pending'; // (pending, accepted, rejected)






    /**
     * اسم ملف الاتفاقية المُولَّدة تلقائياً عند الموافقة.
     * مثال: convention_5_12_20250503_143022.docx
     * يُخزَّن في:  public/conventions/
     */
    #[ORM\Column(length: 255, nullable: true)]
    private ?string $conventionFile = null;

    /** تاريخ ووقت الموافقة على الطلب */
    #[ORM\Column(nullable: true)]
    private ?\DateTimeImmutable $approvedAt = null;

    /** سبب الرفض (اختياري، يُملأ عند الرفض) */
    #[ORM\Column(type: 'text', nullable: true)]
    private ?string $rejectionReason = null;
    /**
     * ★ العلاقة مع التربص — يُنشأ تلقائياً عند موافقة الأدمين
     * OneToOne: كل طلب مقبول = تربص واحد فقط
     */
    #[ORM\OneToOne(mappedBy: 'application', targetEntity: Internship::class, cascade: ['persist', 'remove'])]
    private ?Internship $internship = null;
    // Getters & Setters ────────────────────────────────────────────────────────

    public function getConventionFile(): ?string
    {
        return $this->conventionFile;
    }

    public function setConventionFile(?string $file): static
    {
        $this->conventionFile = $file;
        return $this;
    }

    public function getApprovedAt(): ?\DateTimeImmutable
    {
        return $this->approvedAt;
    }

    public function setApprovedAt(?\DateTimeImmutable $dt): static
    {
        $this->approvedAt = $dt;
        return $this;
    }

    public function getRejectionReason(): ?string
    {
        return $this->rejectionReason;
    }

    public function setRejectionReason(?string $reason): static
    {
        $this->rejectionReason = $reason;
        return $this;
    }
    public function __construct()
    {
        $this->createdAt = new \DateTime();
    }

    // Getters & Setters
    public function getId(): ?int { return $this->id; }

    public function getOffer(): ?Offers { return $this->offer; }
    public function setOffer(?Offers $offer): self { $this->offer = $offer; return $this; }

    public function getStudent(): ?User { return $this->student; }
    public function setStudent(?User $student): self { $this->student = $student; return $this; }

    public function getCreatedAt(): ?\DateTimeInterface { return $this->createdAt; }

    public function getStatus(): ?string { return $this->status; }
    public function setStatus(string $status): self { $this->status = $status; return $this; }



    public function getInternship(): ?Internship
    {
        return $this->internship;
    }

    public function setInternship(?Internship $internship): static
    {
        $this->internship = $internship;
        return $this;
    }

    public function hasInternship(): bool
    {
        return $this->internship !== null;
    }
    // src/Entity/Application.php

    public function getMatchingScore(): int
    {
        $offerSkills = $this->getOffer()->getSkills(); // المهارات المطلوبة في العرض
        $studentSkills = $this->getStudent()->getSkills(); // مهارات الطالب

        if ($offerSkills->isEmpty()) {
            return 0;
        }

        $matchCount = 0;
        foreach ($offerSkills as $oSkill) {
            foreach ($studentSkills as $sSkill) {
                // مقارنة المهارات (افترضنا وجود getName أو getTagName)
                if ($oSkill->getTagName() === $sSkill->getTagName()) {
                    $matchCount++;
                }
            }
        }

        // حساب النسبة المئوية
        return (int) (($matchCount / $offerSkills->count()) * 100);
    }






// ══════════════════════════════════════════════════════════════════
// C) src/Entity/Application.php
// أضيفي هذه الحقول الثلاثة داخل كلاس Application
// ══════════════════════════════════════════════════════════════════

    // ── حقول التقييم بعد انتهاء التربص ──────────────────────────

    /** تقييم الطالب للشركة من 1 إلى 5 نجوم */
    #[ORM\Column(type: 'integer', nullable: true)]
    private ?int $rating = null;

    /** تعليق الطالب على تجربة التربص */
    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $feedback = null;

    /** تاريخ إنهاء التربص (يُملأ يدوياً أو تلقائياً) */
    #[ORM\Column(type: 'datetime', nullable: true)]
    private ?\DateTimeInterface $completedAt = null;

    // ── Getters & Setters ─────────────────────────────────────────

    public function getRating(): ?int
    {
        return $this->rating;
    }

    public function setRating(?int $rating): static
    {
        $this->rating = $rating;
        return $this;
    }

    public function getFeedback(): ?string
    {
        return $this->feedback;
    }

    public function setFeedback(?string $feedback): static
    {
        $this->feedback = $feedback;
        return $this;
    }

    public function getCompletedAt(): ?\DateTimeInterface
    {
        return $this->completedAt;
    }

    public function setCompletedAt(?\DateTimeInterface $completedAt): static
    {
        $this->completedAt = $completedAt;
        return $this;
    }
}