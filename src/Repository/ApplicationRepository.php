<?php
// src/Repository/ApplicationRepository.php

namespace App\Repository;

use App\Entity\Application;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Application>
 */
class ApplicationRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Application::class);
    }

    // =========================================================================
    // ★ Called by AdminDashboardController → Validations tab
    //
    //   Returns applications where:
    //   - status = 'pending_admin'  (company accepted, admin must validate)
    //   - the student belongs to this admin's university
    //
    //   Flow:
    //   Student applies → status='pending'
    //   Company accepts → status='pending_admin'   ← appears here for admin
    //   Admin validates → status='accepted'        → convention generated
    // =========================================================================

    /**
     * @param User $admin  The university admin
     * @return Application[]
     */
    public function findPendingForUniversity(User $admin): array
    {
        return $this->createQueryBuilder('a')
            ->join('a.student', 's')
            ->join('a.offer', 'o')
            ->join('o.company', 'c')
            // The student must be linked to this admin's university
            ->where('s.universityEntity = :admin')
            // Only applications the company already accepted (waiting for admin final OK)
            ->andWhere('a.status = :status')
            ->setParameter('admin', $admin)
            ->setParameter('status', 'pending_admin')
            ->orderBy('a.createdAt', 'DESC')
            ->getQuery()
            ->getResult();
    }

    // =========================================================================
    // ★ Called by AdminDashboardController → Agreements tab
    //
    //   Returns fully validated applications (status = 'accepted') whose
    //   student belongs to this admin.  These have a convention file generated.
    // =========================================================================

    /**
     * @param User $admin
     * @return Application[]
     */
    public function findAcceptedForUniversity(User $admin): array
    {
        return $this->createQueryBuilder('a')
            ->join('a.student', 's')
            ->where('s.universityEntity = :admin')
            ->andWhere('a.status = :status')
            ->setParameter('admin', $admin)
            ->setParameter('status', 'accepted')
            ->orderBy('a.approvedAt', 'DESC')
            ->getQuery()
            ->getResult();
    }
}