<?php
// src/Controller/StudentController.php
// ── فقط الجزء المتعلق بربط الطالب بالأدمين ──────────────────────────────────
// أضف هذا الـ route لصفحة اختيار الجامعة (يُستدعى عند أول دخول للطالب)

namespace App\Controller;

use App\Entity\Application;
use App\Entity\Skills;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use App\Repository\ApplicationRepository;
use App\Entity\Offers;

#[IsGranted('ROLE_STUDENT')]
class StudentController extends AbstractController
{
    // ─── Dashboard ────────────────────────────────────────────────────────────

    #[Route('/student/dashboard', name: 'app_student_dashboard')]
    public function index(EntityManagerInterface $em): Response
    {
        /** @var User $user */
        $user = $this->getUser();

        $acceptedApplication = $em->getRepository(Application::class)->findOneBy([
            'student' => $user, 'status' => 'accepted',
        ]);
        $myInternship = $acceptedApplication?->getOffer();

        $applications = $em->getRepository(Application::class)->findBy(['student' => $user]);

        $stats = ['total' => count($applications), 'pending' => 0, 'accepted' => 0, 'refused' => 0];
        foreach ($applications as $app) {
            $status = strtolower($app->getStatus());
            if (isset($stats[$status])) $stats[$status]++;
        }

        $profileFields     = [$user->getPhone(), $user->getBio(), $user->getUniversity(), $user->getSpecialty(), $user->getProfilePicture()];
        $completionPercent = (count(array_filter($profileFields)) / count($profileFields)) * 100;

        // 1. تعريف المصفوفات
        $offers = [];
        $appliedOfferIds = [];

// 2. جلب كافة العروض التي حالتها 'Active' لجميع الشركات بدون استثناء
        $offers = $em->getRepository(\App\Entity\Offers::class)->findBy(
            ['status' => 'Active'],
            ['createdAt' => 'DESC']
        );

// 3. (اختياري) يمكنكِ إبقاء جلب معلومات الجامعة فقط لعرض اسمها في الواجهة
        $universityAdmin = $user->getUniversityEntity();

// 4. جلب الـ IDs للعروض التي تقدم إليها الطالب (لإظهار حالة "Applied" في الواجهة)
        foreach ($applications as $app) {
            if ($app->getOffer()) {
                $appliedOfferIds[] = $app->getOffer()->getId();
            }
        }


        // ★ IDs العروض التي تقدّم إليها الطالب مسبقاً
        foreach ($applications as $app) {
            if ($app->getOffer()) {
                $appliedOfferIds[] = $app->getOffer()->getId();
            }
        }

        return $this->render('student/dashboard.html.twig', [
            'user'              => $user,
            'stats'             => $stats,
            'completionPercent' => $completionPercent,
            'myInternship'      => $myInternship,
            'myUniversity'      => $user->getUniversityEntity(),
            // ★ جديد
            'offers'            => $offers,
            'appliedOfferIds'   => $appliedOfferIds,
            'applications'      => $applications,  // ★ أضف هذا
            'acceptedApplication' => $acceptedApplication,
        ]);
    }
    // ─── ★ ربط الطالب بأدمين جامعته ──────────────────────────────────────────
    /**
     * POST /student/select-university
     * body JSON: { "universityId": 5 }
     *
     * الطالب يختار جامعته مرة واحدة (أو يغيّرها).
     * يُحفظ في User.universityEntity → يظهر للأدمين في قائمة طلابه.
     */
    #[Route('/student/select-university', name: 'app_student_select_university', methods: ['POST'])]
    public function selectUniversity(Request $request, EntityManagerInterface $em): JsonResponse
    {
        /** @var User $student */
        $student = $this->getUser();

        $data         = json_decode($request->getContent(), true);
        $universityId = $data['universityId'] ?? null;

        if (!$universityId) {
            return $this->json(['error' => 'universityId manquant.'], 400);
        }

        $admin = $em->getRepository(User::class)->find($universityId);

        if (!$admin || !in_array('ROLE_ADMIN', $admin->getRoles(), true)) {
            return $this->json(['error' => 'Université introuvable.'], 404);
        }

        $student->setUniversityEntity($admin);
        $em->flush();

        return $this->json([
            'success'        => true,
            'universityName' => $admin->getUniversityName() ?? $admin->getCompanyName(),
        ]);
    }

    // ─── تحديث الملف الشخصي ──────────────────────────────────────────────────

    #[Route('/student/profile/update', name: 'app_student_profile_update', methods: ['POST'])]
    public function updateProfile(Request $request, EntityManagerInterface $em): JsonResponse
    {
        /** @var User $user */
        $user = $this->getUser();

        $user->setFirstName($request->request->get('firstName')    ?? $user->getFirstName());
        $user->setLastName($request->request->get('lastName')      ?? $user->getLastName());
        $user->setPhone($request->request->get('phone')            ?? $user->getPhone());
        $user->setWilaya($request->request->get('wilaya')          ?? $user->getWilaya());
        $user->setUniversity($request->request->get('university')  ?? $user->getUniversity());
        $user->setSpecialty($request->request->get('specialty')    ?? $user->getSpecialty());
        $user->setBio($request->request->get('bio')                ?? $user->getBio());
        $user->setLevel($request->request->get('level')            ?? $user->getLevel());
        $user->setGithubLink($request->request->get('githubLink')  ?? $user->getGithubLink());
        $user->setPortfolioLink($request->request->get('portfolioLink') ?? $user->getPortfolioLink());

        // صورة الملف الشخصي
        $imageFile = $request->files->get('profilePicture');
        if ($imageFile) {
            $newFilename = uniqid() . '.' . $imageFile->guessExtension();
            $imageFile->move($this->getParameter('kernel.project_dir') . '/public/uploads/profiles', $newFilename);
            $user->setProfilePicture($newFilename);
        }

        // المهارات
        $skillsData = json_decode($request->request->get('skills', '[]'), true);
        if (!empty($skillsData)) {
            foreach ($user->getSkills() as $old) $user->removeSkill($old);
            foreach ($skillsData as $skillName) {
                $skillName = trim($skillName);
                if (!$skillName) continue;
                $skill = $em->getRepository(Skills::class)->findOneBy(['tagName' => $skillName]);
                if (!$skill) {
                    $skill = new Skills();
                    $skill->setTagName($skillName);
                    $em->persist($skill);
                }
                $user->addSkill($skill);
            }
        }





        // المنطق التلقائي لتحديد الجامعة حسب الدومين
        $emailDomain = $request->request->get('emailDomain');
        if ($emailDomain) {
            // البحث عن أدمين (جامعة) ينتهي إيميله بـ @domain.com
            $universityAdmin = $em->getRepository(User::class)->createQueryBuilder('u')
                ->where('u.roles LIKE :role')
                ->andWhere('u.email LIKE :domain')
                ->setParameter('role', '%"ROLE_ADMIN"%')
                ->setParameter('domain', '%@' . $emailDomain)
                ->setMaxResults(1)
                ->getQuery()
                ->getOneOrNullResult();
            if ($universityAdmin) {
                $user->setUniversityEntity($universityAdmin);

                $adminUniName = $universityAdmin->getUniversityName()
                    ?? $universityAdmin->getUniversityRef()?->getName()
                    ?? $universityAdmin->getCompanyName();

                $user->setUniversity($adminUniName);
                $user->setUniversityName($adminUniName);

                if ($universityAdmin->getUniversityRef()) {
                    $user->setStudentUniversityRef($universityAdmin->getUniversityRef());
                }
                if ($universityAdmin->getDepartmentRef()) {
                    $user->setStudentDepartmentRef($universityAdmin->getDepartmentRef());
                }
            }
        }

        $em->flush();
        return new JsonResponse(['status' => 'success']);
    }



    #[Route('/student/offers/{id}/apply', name: 'app_student_apply', methods: ['POST'])]
    public function apply(int $id, EntityManagerInterface $em): JsonResponse
    {
        /** @var User $student */
        $student = $this->getUser();

        $offer = $em->getRepository(\App\Entity\Offers::class)->find($id);
        if (!$offer) {
            return $this->json(['error' => 'Offer not found.'], 404);
        }

        // ✅ التحقق من المقاعد
        if ($offer->isFull()) {
            return $this->json(['error' => 'seats_full', 'message' => 'عذراً، لقد امتلأت جميع المقاعد لهذا العرض.'], 400);
        }

        // تأكد أنه لم يتقدم مسبقاً
        $existing = $em->getRepository(Application::class)->findOneBy([
            'student' => $student,
            'offer'   => $offer,
        ]);
        if ($existing) {
            return $this->json(['error' => 'Already applied.'], 400);
        }

        $application = new Application();
        $application->setStudent($student);
        $application->setOffer($offer);
        $application->setStatus('pending');

        $em->persist($application);
        $em->flush();

        return $this->json([
            'success'         => true,
            'remainingSeats'  => $offer->getRemainingSeats(), // إرجاع المقاعد المتبقية للـ UI
        ]);
    }
    #[Route('/student/offers/{id}/withdraw', name: 'app_student_withdraw', methods: ['POST'])]
    public function withdraw(int $id, EntityManagerInterface $em): JsonResponse
    {
        /** @var User $student */
        $student = $this->getUser();

        $offer = $em->getRepository(\App\Entity\Offers::class)->find($id);
        $application = $em->getRepository(Application::class)->findOneBy([
            'student' => $student,
            'offer'   => $offer,
        ]);

        if (!$application) {
            return $this->json(['error' => 'Application not found.'], 404);
        }
        if ($application->getStatus() !== 'pending') {
            return $this->json(['error' => 'Cannot withdraw after processing.'], 400);
        }

        $em->remove($application);
        $em->flush();

        return $this->json(['success' => true]);
    }








    #[Route('/student/applications/{id}/convention', name: 'app_student_download_convention')]
    public function downloadConvention(int $id, EntityManagerInterface $em): Response
    {
        /** @var User $student */
        $student = $this->getUser();

        $application = $em->getRepository(Application::class)->find($id);

        if (!$application || $application->getStudent() !== $student) {
            throw $this->createNotFoundException();
        }
        if (!$application->getConventionFile()) {
            throw $this->createNotFoundException('Convention file not found.');
        }

        $filePath = $this->getParameter('kernel.project_dir')
            . '/public/conventions/' . $application->getConventionFile();

        if (!file_exists($filePath)) {
            throw $this->createNotFoundException('File not found on disk.');
        }

        $response = new \Symfony\Component\HttpFoundation\BinaryFileResponse($filePath);
        $response->setContentDisposition(
            \Symfony\Component\HttpFoundation\ResponseHeaderBag::DISPOSITION_ATTACHMENT,
            'convention_' . $application->getStudent()->getLastName() . '.docx'
        );
        $response->headers->set(
            'Content-Type',
            'application/vnd.openxmlformats-officedocument.wordprocessingml.document'
        );
        return $response;
    }















    // src/Controller/StudentController.php

    #[Route('/student/applications/{id}/delete', name: 'app_student_application_delete', methods: ['POST'])]
    public function deleteApplication(int $id, ApplicationRepository $appRepo, EntityManagerInterface $em): JsonResponse
    {
        $application = $appRepo->find($id);

        // تأكدي أن الطلب يخص الطالب المسجل حالياً ومرفوض فعلياً
        if (!$application || $application->getStudent() !== $this->getUser()) {
            return new JsonResponse(['success' => false, 'error' => 'Application not found'], 404);
        }

        $em->remove($application);
        $em->flush();

        return new JsonResponse(['success' => true]);
    }

    // داخل StudentController.php

    #[Route('/student/browse', name: 'app_student_browse_offers')]
    public function browseOffers(EntityManagerInterface $em): Response
    {
        // جلب العروض مرتبة من الأحدث أولاً
        $allOffers = $em->getRepository(Offers::class)->findBy([], ['id' => 'DESC']);

        // تصفية العروض بناءً على المنطق الذكي الذي وضعناه في الـ Entity
        $activeOffers = array_filter($allOffers, function($offer) {
            return $offer->isActive();
        });

        return $this->render('student/browse.html.twig', [
            'offers' => $activeOffers,
        ]);
    }
}


