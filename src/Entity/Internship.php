<?php

namespace App\Entity;

use App\Repository\InternshipRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * جدول التربصات الفعلية.
 *
 * يُنشأ تلقائياً في AdminDashboardController::approveApplication()
 * عندما يوافق الأدمين على الطلب (status → accepted).
 *
 * الحالات المحسوبة (getComputedStatus):
 *  - 'pending'   → الأدمين وافق لكن internshipStart لم يصل بعد
 *  - 'active'    → internshipStart وصل والتربص جارٍ
 *  - 'completed' → تجاوزنا endDate → التربص انتهى
 *  - 'cancelled' → مُلغى يدوياً
 */
#[ORM\Entity(repositoryClass: InternshipRepository::class)]
#[ORM\Table(name: 'internships')]
class Internship
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    /**
     * ربط 1:1 مع Application — كل تربص = طلب مقبول واحد
     */
    #[ORM\OneToOne(targetEntity: Application::class, inversedBy: 'internship')]
    #[ORM\JoinColumn(nullable: false, onDelete: 'CASCADE')]
    private ?Application $application = null;

    /**
     * تاريخ بداية التربص — يُؤخذ من offer.internshipStart عند الإنشاء
     */
    #[ORM\Column(type: 'date')]
    private ?\DateTimeInterface $startDate = null;

    /**
     * تاريخ نهاية التربص — يُحسب من startDate + offer.durationMonths
     * إذا لم تكن المدة محددة يبقى null
     */
    #[ORM\Column(type: 'date', nullable: true)]
    private ?\DateTimeInterface $endDate = null;

    /**
     * الحالة اليدوية الوحيدة التي تُحفظ: cancelled فقط
     * باقي الحالات محسوبة في getComputedStatus()
     * القيم: active | cancelled
     */
    #[ORM\Column(length: 20)]
    private string $status = 'active';

    #[ORM\Column(type: 'datetime')]
    private ?\DateTimeInterface $createdAt = null;

    // =========================================================
    // Constructor
    // =========================================================

    public function __construct()
    {
        $this->createdAt = new \DateTime();
    }

    // =========================================================
    // ★ STATUS LOGIC
    // =========================================================

    /**
     * الحالة المحسوبة للتربص:
     *  - 'cancelled'  → مُلغى يدوياً
     *  - 'pending'    → الأدمين وافق لكن التربص لم يبدأ بعد (internshipStart في المستقبل)
     *  - 'active'     → التربص جارٍ حالياً
     *  - 'completed'  → التربص انتهى (تجاوزنا endDate)
     */
    public function getComputedStatus(): string
    {
        if ($this->status === 'cancelled') {
            return 'cancelled';
        }

        $now = new \DateTime('today');

        // انتهى التربص
        if ($this->endDate !== null && $this->endDate < $now) {
            return 'completed';
        }

        // التربص جارٍ
        if ($this->startDate !== null && $this->startDate <= $now) {
            return 'active';
        }

        // لم يبدأ بعد
        return 'pending';
    }

    public function isActive(): bool
    {
        return $this->getComputedStatus() === 'active';
    }

    public function isCompleted(): bool
    {
        return $this->getComputedStatus() === 'completed';
    }

    // =========================================================
    // Getters & Setters
    // =========================================================

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getApplication(): ?Application
    {
        return $this->application;
    }

    public function setApplication(?Application $application): static
    {
        $this->application = $application;
        return $this;
    }

    public function getStartDate(): ?\DateTimeInterface
    {
        return $this->startDate;
    }

    public function setStartDate(?\DateTimeInterface $startDate): static
    {
        $this->startDate = $startDate;
        return $this;
    }

    public function getEndDate(): ?\DateTimeInterface
    {
        return $this->endDate;
    }

    public function setEndDate(?\DateTimeInterface $endDate): static
    {
        $this->endDate = $endDate;
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

    public function getCreatedAt(): ?\DateTimeInterface
    {
        return $this->createdAt;
    }

    // ── Helpers ────────────────────────────────────────────────

    public function getStudentFullName(): string
    {
        return $this->application?->getStudent()?->getFullName() ?? '—';
    }

    public function getOfferTitle(): string
    {
        return $this->application?->getOffer()?->getTitle() ?? '—';
    }

    public function getCompanyName(): string
    {
        return $this->application?->getOffer()?->getCompany()?->getCompanyName() ?? '—';
    }
}