<?php

namespace App\Repository;

use App\Entity\VerificationRequest;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<VerificationRequest>
 */
class VerificationRequestRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, VerificationRequest::class);
    }

    /**
     * جلب كل الطلبات الخاصة بشركة معينة مرتبة من الأحدث للأقدم
     */
    public function findByCompany(\App\Entity\User $company): array
    {
        return $this->createQueryBuilder('vr')
            ->andWhere('vr.company = :company')
            ->setParameter('company', $company)
            ->orderBy('vr.createdAt', 'DESC')
            ->getQuery()
            ->getResult();
    }

    /**
     * جلب كل الطلبات المعلقة (للوحة الإدارة)
     */
    public function findPending(): array
    {
        return $this->createQueryBuilder('vr')
            ->andWhere('vr.status = :status')
            ->setParameter('status', 'Pending')
            ->orderBy('vr.createdAt', 'DESC')
            ->getQuery()
            ->getResult();
    }
}