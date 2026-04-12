<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Doctrine\ORM\EntityManagerInterface;

#[IsGranted('ROLE_STUDENT')] // حماية الصفحة (فقط للطلاب)
class StudentController extends AbstractController
{
    #[Route('/student/dashboard', name: 'app_student_dashboard')]
    public function index(): Response
    {
        $user = $this->getUser(); // جلب بيانات الطالب المسجل حالياً

        return $this->render('student/dashboard.html.twig', [
            'user' => $user,
        ]);
    }




    // src/Controller/StudentController.php

    #[Route('/student/profile/update', name: 'app_student_profile_update', methods: ['POST'])]
    public function updateProfile(Request $request, EntityManagerInterface $entityManager): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        $user = $this->getUser();

        if (!$user) {
            return new JsonResponse(['status' => 'error', 'message' => 'User not found'], 403);
        }

        // تفعيل وتحديث كل الحقول التي أضفتها
        $user->setFirstName($data['firstName'] ?? $user->getFirstName());
        $user->setLastName($data['lastName'] ?? $user->getLastName());
        $user->setPhone($data['phone'] ?? $user->getPhone()); // تفعيل هذا السطر
        $user->setWilaya($data['wilaya'] ?? $user->getWilaya()); // إضافة هذا السطر
        $user->setBio($data['bio'] ?? $user->getBio()); // إضافة هذا السطر
        $user->setSkills($data['skills'] ?? []); // تفعيل هذا السطر
        $user->setSpecialty($data['specialty'] ?? $user->getSpecialty());
        $user->setLevel($data['level'] ?? $user->getLevel());

        $entityManager->persist($user);
        $entityManager->flush();

        return new JsonResponse(['status' => 'success', 'message' => 'Profile updated successfully!']);
    }}
