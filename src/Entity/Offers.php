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

    #[ORM\Column]  // ← هذا هو الحل
    private ?int $id = null;


    // الربط مع كينونة المستخدم (الشركة)
    #[ORM\ManyToOne(targetEntity: User::class)]
    #[ORM\JoinColumn(name: "company_id", referencedColumnName: "id", nullable: false, onDelete: "CASCADE")]
    private ?User $company = null;

    #[ORM\Column(length: 255)]
    private ?string $title = null; // [cite: 47, 48]

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $description = null; // [cite: 54]

    //  #[ORM\Column(type: Types::TEXT, nullable: true)]
    //  private ?string $skills = null; // لتخزين المهارات كنص أو JSON [cite: 50, 131]

    #[ORM\Column(length: 100, nullable: true)]
    private ?string $duration = null; // [cite: 55]

    #[ORM\Column(length: 50)]
    private ?string $locationType = 'PFE'; // Hybrid, On-site, Remote [cite: 57, 58]

    #[ORM\Column(length: 100, nullable: true)]
    private ?string $wilaya = null; // [cite: 59]

    #[ORM\Column(type: Types::DATE_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $deadline = null; // [cite: 61, 62]








    #[ORM\ManyToMany(targetEntity: Skills::class)]
    #[ORM\JoinTable(name: 'offers_skills')]
    #[ORM\JoinColumn(name: 'offer_id', referencedColumnName: 'id')]
    #[ORM\InverseJoinColumn(name: 'skill_id', referencedColumnName: 'id_skill')]
    private Collection $skills;
    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 8, nullable: true)]
    private ?string $latitude = null; // [cite: 65, 145]

    #[ORM\Column(type: Types::DECIMAL, precision: 11, scale: 8, nullable: true)]
    private ?string $longitude = null; // [cite: 66, 145]

    #[ORM\Column(length: 20)]
    private ?string $status = 'Active'; // Active, Draft, Closed [cite: 42, 73]

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $createdAt = null;


// ...

    #[ORM\Column(type: 'date', nullable: true)]
    private ?\DateTimeInterface $startDate = null; // تاريخ بداية العرض

    #[ORM\Column(type: 'date', nullable: true)]
    private ?\DateTimeInterface $internshipStart = null; // تاريخ بداية التربص
    // ... الخصائص الأخرى

    #[ORM\OneToMany(mappedBy: 'offer', targetEntity: Application::class)]
    private Collection $applications;

// أضف هذا الحقل بعد locationType
    #[ORM\Column(length: 50, nullable: true)]
    private ?string $level = null; // Master, Licence, Master/Licence

// Getter/Setter
    public function getLevel(): ?string { return $this->level; }
    public function setLevel(?string $level): static { $this->level = $level; return $this; }

    /**
     * @return Collection<int, Application>
     */
    public function getApplications(): Collection
    {
        return $this->applications;
    }

// --- Getters & Setters ---

    public function getStartDate(): ?\DateTimeInterface
    {
        return $this->startDate;
    }

    public function setStartDate(?\DateTimeInterface $startDate): self
    {
        $this->startDate = $startDate;
        return $this;
    }

    public function getInternshipStart(): ?\DateTimeInterface
    {
        return $this->internshipStart;
    }

    public function setInternshipStart(?\DateTimeInterface $internshipStart): self
    {
        $this->internshipStart = $internshipStart;
        return $this;
    }

// ...
   // public function __construct()
    //{
        //$this->createdAt = new \DateTimeImmutable();
    //}



    public function __construct()
    {
        // تهيئة التوقيت الحالي
        $this->createdAt = new \DateTime();

        // تعيين الحالة الافتراضية
        $this->status = 'Active';

        // تهيئة مجموعات البيانات (Collections)
        $this->skills = new ArrayCollection();
        $this->applications = new ArrayCollection();
    }

    // Getters and Setters ...
    public function getId(): ?int { return $this->id; }

    public function getCompany(): ?User { return $this->company; }
    public function setCompany(?User $company): self { $this->company = $company; return $this; }

    public function getTitle(): ?string { return $this->title; }
    public function setTitle(string $title): self { $this->title = $title; return $this; }

    // أكمل بقية الـ Getters والـ Setters لكل الحقول بنفس الطريقة...







    // --- Getters & Setters ---







    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): static
    {
        $this->description = $description;
        return $this;
    }

    //  public function getSkills(): ?string
    //  {
    //      return $this->skills;
    //  }

    //  public function setSkills(?string $skills): static
   //  {
        //   $this->skills = $skills;
        //  return $this;
        // }

    public function getDuration(): ?string
    {
        return $this->duration;
    }

    public function setDuration(?string $duration): static
    {
        $this->duration = $duration;
        return $this;
    }

    public function getLocationType(): ?string
    {
        return $this->locationType;
    }

    public function setLocationType(string $locationType): static
    {
        $this->locationType = $locationType;
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

    public function getDeadline(): ?\DateTimeInterface
    {
        return $this->deadline;
    }

    public function setDeadline(?\DateTimeInterface $deadline): static
    {
        $this->deadline = $deadline;
        return $this;
    }

    public function getLatitude(): ?string
    {
        return $this->latitude;
    }

    public function setLatitude(?string $latitude): static
    {
        $this->latitude = $latitude;
        return $this;
    }

    public function getLongitude(): ?string
    {
        return $this->longitude;
    }

    public function setLongitude(?string $longitude): static
    {
        $this->longitude = $longitude;
        return $this;
    }

    // ✅ صحيح
    public function getStatus(): ?string
    {
        return $this->status; // ← هكذا
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

    public function setCreatedAt(\DateTimeInterface $createdAt): static
    {
        $this->createdAt = $createdAt;
        return $this;
    }


    // أضيفي هذه الـ methods
    public function getSkills(): Collection
    {
        return $this->skills;
    }

    public function addSkill(Skills $skill): static
    {
        if (!$this->skills->contains($skill)) {
            $this->skills->add($skill);
        }
        return $this;
    }

    public function removeSkill(Skills $skill): static
    {$this->skills->removeElement($skill);
        return $this;
    }















// src/Entity/Offers.php

    public function calculateStatus(Collection $applications): string
    {
        $now = new \DateTime();

        // 1. إذا لم يحن موعد البداية بعد، فهو نشط (Active) للتقديم
        if ($this->startDate > $now) {
            return 'active';
        }

        // 2. إذا حل موعد البداية، نبحث عن وجود أي طالب مقبول
        foreach ($applications as $app) {
            if ($app->getStatus() === 'accepted') {
                // إذا وجدنا مقبولين، يمكن اعتباره "مكتمل" أو يبقى نشطاً حسب رغبتك
                // لكن بناءً على طلبك، الحالة تصبح Inactive فقط إذا لم يوجد مقبولين
                return 'active';
            }
        }

        // 3. حل الموعد ولا يوجد مقبولين
        return 'inactive';
    }




    // src/Entity/Offers.php

    public function isCurrentlyActive(): bool
    {
        $now = new \DateTime();

        // إذا لم يحن تاريخ البدء بعد، يبقى العرض نشطاً كـ "عرض"
        if ($this->startDate > $now) {
            return true;
        }

        // إذا حل تاريخ البدء، نتحقق من وجود طلبة مقبولين (Accepted)
        // نفترض أن لديك علاقة applications في الكيان
        foreach ($this->getApplications() as $application) {
            if ($application->getStatus() === 'accepted') {
                return true; // يعتبر تربصاً جارياً
            }
        }

        return false; // تاريخ البدء مرّ ولا يوجد مقبوضين، يصبح غير نشط
    }
}