<?php

namespace App\Repository;

use App\Entity\Skill;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Skill>
 *
 * @method Skill|null find($id, $lockMode = null, $lockVersion = null)
 * @method Skill|null findOneBy(array $criteria, array $orderBy = null)
 * @method Skill[]    findAll()
 * @method Skill[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class SkillRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Skill::class);
    }

    /**
     * للبحث عن المهارات بناءً على الحروف التي يكتبها المستخدم في حقل الإكمال التلقائي
     *
     * @param string $query النص المكتوب (مثلاً: "php" أو "react")
     * @return Skill[] يعيد مصفوفة من المهارات المطابقة
     */
    public function findBySearchQuery(string $query): array
    {
        return $this->createQueryBuilder('s')
            ->select('s', 'c') // جلب المهارة والتصنيف معاً في استعلام واحد لتقليل الضغط
            ->join('s.category', 'c')
            ->where('s.name LIKE :search')
            ->setParameter('search', '%' . $query . '%')
            ->orderBy('s.name', 'ASC')
            ->setMaxResults(10) // تحديد النتائج بـ 10 لسرعة الاستجابة
            ->getQuery()
            ->getResult();
    }

    /**
     * لحساب تقاطع المهارات بين الطالب وعرض العمل في الـ Smart Matching
     *
     * @param array $skillIds مصفوفة الـ IDs لمهارات العرض
     * @return Skill[]
     */
    public function findSkillsByIds(array $skillIds): array
    {
        return $this->createQueryBuilder('s')
            ->where('s.id IN (:ids)')
            ->setParameter('ids', $skillIds)
            ->getQuery()
            ->getResult();
    }
}