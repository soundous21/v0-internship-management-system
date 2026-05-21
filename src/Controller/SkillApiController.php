<?php
namespace App\Controller;

use App\Repository\SkillRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class SkillApiController extends AbstractController
{
    /**
     * @Route("/api/skills/search", name="api_skills_search", methods={"GET"})
     */
    public function search(Request $request, SkillRepository $skillRepository): JsonResponse
    {
        $query = $request->query->get('q', '');

        if (strlen($query) < 1) {
            return new JsonResponse([]);
        }

        // ابحث عن المهارات التي تبدأ أو تحتوي على النص المكتوب
        $skills = $skillRepository->findBySearchQuery($query);

        $data = [];
        foreach ($skills as $skill) {
            $data[] = [
                'id' => $skill->getId(),
                'text' => $skill->getName(),
                'category' => $skill->getCategory()->getName() // لإظهار القسم التابع له لتسهيل الاختيار
            ];
        }

        return new JsonResponse($data);
    }

    /**
     * @Route("/api/skills/suggest", name="api_skills_suggest", methods={"POST"})
     */
    public function suggest(Request $request): JsonResponse
    {
        // منطق استقبال المهارات المقترحة وحفظها في جدول suggested_skill بوضعية pending
        $data = json_decode($request->getContent(), true);

        // هنا نقوم بحفظ المهارة المقترحة في قاعدة البيانات عبر EntityManager...

        return new JsonResponse(['success' => true, 'message' => 'تم إرسال اقتراحك للإدارة بنجاح.']);
    }
}