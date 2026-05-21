<?php
// src/Controller/SkillController.php
// ══════════════════════════════════════════════════════════════════
// الـ Controller المسؤول عن:
//   GET  /api/skills/search?q=php   → إكمال تلقائي للـ TomSelect
//   POST /api/skills/suggest        → اقتراح مهارة جديدة للإدارة
// ══════════════════════════════════════════════════════════════════

namespace App\Controller;

use App\Entity\SuggestedSkill;
use App\Repository\SkillRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;

class SkillController extends AbstractController
{
    // ─── 1. بحث سريع للـ TomSelect ──────────────────────────────────────────

    #[Route('/api/skills/search', name: 'api_skills_search', methods: ['GET'])]
    public function search(Request $request, SkillRepository $repo): JsonResponse
    {
        $query = trim($request->query->get('q', ''));

        if (strlen($query) < 1) {
            return $this->json([]);
        }

        $skills = $repo->findBySearchQuery($query);

        // تنسيق النتائج ليفهمها TomSelect
        // valueField: 'id'  →  يُرسَل للـ server عند الحفظ
        // labelField: 'text' → يُعرض للمستخدم
        $results = array_map(fn($s) => [
            'id'       => $s->getId(),                         // Skill.id (الهرمي الجديد)
            'text'     => $s->getName(),                       // اسم المهارة
            'category' => $s->getCategory()?->getName() ?? '', // اسم القسم
        ], $skills);

        return $this->json($results);
    }

    // ─── 2. اقتراح مهارة جديدة للإدارة ─────────────────────────────────────

    #[Route('/api/skills/suggest', name: 'api_skills_suggest', methods: ['POST'])]
    public function suggest(Request $request, EntityManagerInterface $em): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        $name = trim($data['name'] ?? '');

        if (!$name) {
            return $this->json(['error' => 'Skill name is required.'], 400);
        }

        // تجنب الاقتراحات المكررة
        $existing = $em->getRepository(SuggestedSkill::class)
            ->findOneBy(['name' => $name]);

        if ($existing) {
            return $this->json([
                'message' => 'This skill was already suggested and is under review.',
                'status'  => $existing->getStatus(),
            ]);
        }

        $suggestion = new SuggestedSkill();
        $suggestion->setName($name);
        $suggestion->setStatus('pending');

        $em->persist($suggestion);
        $em->flush();

        return $this->json([
            'success' => true,
            'message' => "\"$name\" has been submitted for admin review. Thank you!",
        ]);
    }
}