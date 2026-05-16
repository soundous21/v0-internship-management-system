<?php

namespace App\Entity;

use App\Repository\OffersRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use App\Entity\Skills;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

#[ORM\Entity(repositoryClass: OffersRepository::class)]
#[ORM\Table(name: 'offers')]
class Offers
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(targetEntity: User::class)]
    #[ORM\JoinColumn(name: "company_id", referencedColumnName: "id", nullable: false, onDelete: "CASCADE")]
    private ?User $company = null;

    #[ORM\Column(length: 255)]
    private ?string $title = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $description = null;

    #[ORM\Column(length: 100, nullable: true)]
    private ?string $duration = null;

    /**
     * نوع العرض: PFE, Hybrid, On-site, Remote
     */
    #[ORM\Column(length: 50)]
    private ?string $locationType = 'PFE';

    #[ORM\Column(length: 100, nullable: true)]
    private ?string $wilaya = null;

    #[ORM\Column(type: Types::DATE_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $deadline = null;

    #[ORM\ManyToMany(targetEntity: Skills::class)]
    #[ORM\JoinTable(name: 'offers_skills')]
    #[ORM\JoinColumn(name: 'offer_id', referencedColumnName: 'id')]
    #[ORM\InverseJoinColumn(name: 'skill_id', referencedColumnName: 'id_skill')]
    private Collection $skills;

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 8, nullable: true)]
    private ?string $latitude = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 11, scale: 8, nullable: true)]
    private ?string $longitude = null;

    /**
     * الحالة اليدوية التي تحفظها الشركة: Active | Draft | Closed
     * لا تُعدَّل هذه القيمة تلقائياً في أي Controller.
     */
    #[ORM\Column(length: 20)]
    private ?string $status = 'Active';

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $createdAt = null;

    /**
     * تاريخ نشر العرض (بداية استقبال الطلبات)
     */
    #[ORM\Column(type: 'date', nullable: true)]
    private ?\DateTimeInterface $startDate = null;

    /**
     * تاريخ بداية التربص الفعلي (بعد القبول)
     */
    #[ORM\Column(type: 'date', nullable: true)]
    private ?\DateTimeInterface $internshipStart = null;

    #[ORM\OneToMany(mappedBy: 'offer', targetEntity: Application::class)]
    private Collection $applications;

    #[ORM\Column(length: 50, nullable: true)]
    private ?string $level = null;

    #[ORM\Column(type: 'integer', nullable: true)]
    private ?int $seats = null;

    // =========================================================
    // Constructor
    // =========================================================

    public function __construct()
    {
        $this->createdAt    = new \DateTime();
        $this->status       = 'Active';
        $this->skills       = new ArrayCollection();
        $this->applications = new ArrayCollection();
    }

    // =========================================================
    // ★ STATUS LOGIC — مكان واحد لكل المنطق
    // =========================================================

    /**
     * الحالة المحسوبة الواقعية للعرض.
     *
     * القيم الممكنة:
     *  - 'Draft'        → العرض مسوّدة، لم يُنشر بعد
     *  - 'Active'       → العرض نشط ويستقبل طلبات (قبل الـ deadline)
     *  - 'Closed'       → أُغلق يدوياً من قِبل الشركة
     *  - 'Deadline Over'→ انتهى الموعد النهائي ولم يتم تربص بعد
     *  - 'In Progress'  → التربص جارٍ (تجاوزنا internshipStart وهناك مقبولون)
     *  - 'Expired'      → تجاوزنا internshipStart ولا يوجد أي مقبول
     */
    public function getComputedStatus(): string
    {
        // 1. مسوّدة
        if ($this->status === 'Draft') {
            return 'Draft';
        }

        // 2. مغلق يدوياً
        if ($this->status === 'Closed') {
            return 'Closed';
        }

        $now = new \DateTime('today'); // نقارن بالتاريخ فقط بدون وقت

        // 3. هل تجاوزنا تاريخ بداية التربص؟
        if ($this->internshipStart !== null && $this->internshipStart <= $now) {
            // هل يوجد طلاب مقبولون (accepted)?
            foreach ($this->applications as $app) {
                if ($app->getStatus() === 'accepted') {
                    return 'In Progress'; // تربص جارٍ
                }
            }
            return 'Expired'; // مرّ التاريخ ولا مقبول
        }

        // 4. هل انتهى الـ deadline للتقديم؟
        if ($this->deadline !== null && $this->deadline < $now) {
            return 'Deadline Over';
        }

        // 5. العرض نشط بشكل طبيعي
        return 'Active';
    }

    /**
     * هل العرض مؤهل للظهور للطلاب وقبول طلبات جديدة؟
     *
     * يُستخدم في StudentController::browseOffers() وأي مكان آخر.
     */
    public function isActive(): bool
    {
        return $this->getComputedStatus() === 'Active';
    }

    /**
     * هل التربص جارٍ حالياً؟
     */
    public function isInProgress(): bool
    {
        return $this->getComputedStatus() === 'In Progress';
    }

    // =========================================================
    // Seats helpers
    // =========================================================

    /**
     * المقاعد المتبقية (null = لا حد)
     */
    public function getRemainingSeats(): ?int
    {
        if ($this->seats === null) {
            return null;
        }

        $acceptedCount = 0;
        foreach ($this->applications as $app) {
            if ($app->getStatus() === 'accepted') {
                $acceptedCount++;
            }
        }

        return max(0, $this->seats - $acceptedCount);
    }

    /**
     * هل المقاعد ممتلئة؟
     */
    public function isFull(): bool
    {
        if ($this->seats === null) {
            return false;
        }
        return $this->getRemainingSeats() === 0;
    }

    // =========================================================
    // Getters & Setters
    // =========================================================

    public function getId(): ?int { return $this->id; }

    public function getCompany(): ?User { return $this->company; }
    public function setCompany(?User $company): static { $this->company = $company; return $this; }

    public function getTitle(): ?string { return $this->title; }
    public function setTitle(string $title): static { $this->title = $title; return $this; }

    public function getDescription(): ?string { return $this->description; }
    public function setDescription(?string $description): static { $this->description = $description; return $this; }

    public function getDuration(): ?string { return $this->duration; }
    public function setDuration(?string $duration): static { $this->duration = $duration; return $this; }

    public function getLocationType(): ?string { return $this->locationType; }
    public function setLocationType(string $locationType): static { $this->locationType = $locationType; return $this; }

    public function getWilaya(): ?string { return $this->wilaya; }
    public function setWilaya(?string $wilaya): static { $this->wilaya = $wilaya; return $this; }

    public function getDeadline(): ?\DateTimeInterface { return $this->deadline; }
    public function setDeadline(?\DateTimeInterface $deadline): static { $this->deadline = $deadline; return $this; }

    public function getLatitude(): ?string { return $this->latitude; }
    public function setLatitude(?string $latitude): static { $this->latitude = $latitude; return $this; }

    public function getLongitude(): ?string { return $this->longitude; }
    public function setLongitude(?string $longitude): static { $this->longitude = $longitude; return $this; }

    /**
     * الحالة اليدوية (Active | Draft | Closed فقط)
     * لا تستخدم هذه للعرض في الواجهة — استخدم getComputedStatus()
     */
    public function getStatus(): ?string { return $this->status; }
    public function setStatus(string $status): static { $this->status = $status; return $this; }

    public function getCreatedAt(): ?\DateTimeInterface { return $this->createdAt; }
    public function setCreatedAt(\DateTimeInterface $createdAt): static { $this->createdAt = $createdAt; return $this; }

    public function getStartDate(): ?\DateTimeInterface { return $this->startDate; }
    public function setStartDate(?\DateTimeInterface $startDate): static { $this->startDate = $startDate; return $this; }

    public function getInternshipStart(): ?\DateTimeInterface { return $this->internshipStart; }
    public function setInternshipStart(?\DateTimeInterface $internshipStart): static { $this->internshipStart = $internshipStart; return $this; }

    public function getLevel(): ?string { return $this->level; }
    public function setLevel(?string $level): static { $this->level = $level; return $this; }

    public function getSeats(): ?int { return $this->seats; }
    public function setSeats(?int $seats): static { $this->seats = $seats; return $this; }

    // ── Skills ──────────────────────────────────────────────────────

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

    // ── Applications ────────────────────────────────────────────────

    public function getApplications(): Collection { return $this->applications; }
}