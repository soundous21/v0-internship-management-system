<?php

namespace App\Repository;

use App\Entity\Internship;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Internship>
 */
class InternshipRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Internship::class);
    }

    /**
     * جلب كل التربصات الخاصة بطلاب جامعة معينة (للأدمين)
     */
    public function findByUniversity(User $admin): array
    {
        return $this->createQueryBuilder('i')
            ->join('i.application', 'a')
            ->join('a.student', 's')
            ->where('s.universityEntity = :admin')
            ->setParameter('admin', $admin)
            ->orderBy('i.startDate', 'DESC')
            ->getQuery()
            ->getResult();
    }

    /**
     * جلب التربصات النشطة حالياً (startDate وصل وendDate لم يمر)
     */
    public function findActive(): array
    {
        $now = new \DateTime('today');

        return $this->createQueryBuilder('i')
            ->where('i.status != :cancelled')
            ->andWhere('i.startDate <= :now')
            ->andWhere('i.endDate IS NULL OR i.endDate >= :now')
            ->setParameter('cancelled', 'cancelled')
            ->setParameter('now', $now)
            ->getQuery()
            ->getResult();
    }

    /**
     * جلب التربصات المنتهية (تجاوزنا endDate)
     */
    public function findCompleted(): array
    {
        $now = new \DateTime('today');

        return $this->createQueryBuilder('i')
            ->where('i.endDate IS NOT NULL')
            ->andWhere('i.endDate < :now')
            ->andWhere('i.status != :cancelled')
            ->setParameter('now', $now)
            ->setParameter('cancelled', 'cancelled')
            ->getQuery()
            ->getResult();
    }
}