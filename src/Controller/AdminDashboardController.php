<?php
// src/Controller/AdminDashboardController.php
namespace App\Controller;

use App\Entity\Application;
use App\Entity\Internship;
use App\Entity\User;
use App\Entity\VerificationRequest;
use App\Service\ConventionGeneratorService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted('ROLE_ADMIN')]
class AdminDashboardController extends AbstractController
{ #[Route('/admin/dashboard', name: 'app_admin_dashboard')]
public function dashboard(EntityManagerInterface $em): Response
{
    /** @var User $admin */
    $admin = $this->getUser();

    // داخل دالة dashboard في AdminDashboardController.php

    $chartLabels = [];
    $chartData = [];

// تغيير الرقم من 7 إلى 3 لجلب 4 أسابيع فقط
    for ($i = 3; $i >= 0; $i--) {
        $date = (new \DateTime())->modify("-$i weeks");

        // عرض رقم الأسبوع
        $chartLabels[] = 'Week ' . $date->format('W');

        // جلب بداية ونهاية الأسبوع
        $startOfWeek = (clone $date)->modify('monday this week')->setTime(0, 0);
        $endOfWeek = (clone $date)->modify('sunday this week')->setTime(23, 59, 59);

        $count = $em->getRepository(Application::class)->createQueryBuilder('a')
            ->select('count(a.id)')
            ->where('a.createdAt >= :start')
            ->andWhere('a.createdAt <= :end')
            ->setParameter('start', $startOfWeek)
            ->setParameter('end', $endOfWeek)
            ->getQuery()
            ->getSingleScalarResult();

        $chartData[] = $count;
    }
    // --- 2. بيانات الدائرة (Student Status) ---
    $statusStats = [
        'pending' => $em->getRepository(Application::class)->count(['status' => 'pending']),
        'accepted' => $em->getRepository(Application::class)->count(['status' => 'accepted']),
        'rejected' => $em->getRepository(Application::class)->count(['status' => 'rejected']),
    ];
// 1. Fetch pending applications
    $pendingApps = $em->getRepository(Application::class)
        ->findPendingForUniversity($admin);

    $acceptedApps = $em->getRepository(Application::class)
        ->createQueryBuilder('a')
        ->join('a.student', 's')
        ->where('s.universityEntity = :admin') // نستخدم universityEntity المعرف في كيان User
        ->andWhere('a.status = :status')
        ->setParameter('admin', $admin)
        ->setParameter('status', 'accepted')
        ->getQuery()
        ->getResult();

    $allRequests = $em->getRepository(VerificationRequest::class)->findBy(
        ['university' => $admin], ['createdAt' => 'DESC']
    );

    $pendingApps = $em->getRepository(Application::class)
        ->findPendingForUniversity($admin);

    $pendingVerifications = $em->getRepository(VerificationRequest::class)->findBy([
        'university' => $admin, 'status' => 'Pending',
    ]);
// ★ الخطوة المفقودة: جلب كافة طلبات التحقق لتمريرها للمتغير allRequests
    $allRequests = $em->getRepository(VerificationRequest::class)->findBy(
        ['university' => $admin], ['createdAt' => 'DESC']
    );



    // ... (الكود الموجود مسبقاً) ...

    // --- حساب إحصائيات الطلاب (Placed vs Unplaced) ---
    // الطلاب المقبولين: هم الذين لديهم طلب واحد على الأقل بحالة 'accepted'
    $placedCount = $em->getRepository(Application::class)->createQueryBuilder('a')
        ->select('count(DISTINCT s.id)')
        ->join('a.student', 's')
        ->where('s.universityEntity = :admin')
        ->andWhere('a.status = :status')
        ->setParameter('admin', $admin)
        ->setParameter('status', 'accepted')
        ->getQuery()->getSingleScalarResult();

    $totalStudents = $admin->getStudents()->count();
    $unplacedCount = $totalStudents - $placedCount;
 // حساب حالات الطلبات
    $statusCounts = $em->getRepository(Application::class)->createQueryBuilder('a')
        ->select('a.status, count(a.id) as count')
        ->join('a.student', 's')
        ->where('s.universityEntity = :admin')
        ->setParameter('admin', $admin)
        ->groupBy('a.status')
        ->getQuery()->getArrayResult();
    $statusData = ['pending' => 0, 'accepted' => 0, 'refused' => 0];
    foreach ($statusCounts as $row) {
        $statusData[$row['status']] = (int)$row['count'];
    }

// --- إحصائيات حالة طلبات الطلاب ---
    $applicationRepo = $em->getRepository(Application::class);

// جلب الإحصائيات من قاعدة البيانات
    $statusCounts = $applicationRepo->createQueryBuilder('a')
        ->select('a.status, COUNT(a.id) as count')
        ->groupBy('a.status')
        ->getQuery()
        ->getResult();

    $appStatusLabels = [];
    $appStatusData = [];

    foreach ($statusCounts as $stat) {
        // ترجمة الحالات إذا أردت أو عرضها كما هي
        $appStatusLabels[] = ucfirst($stat['status']);
        $appStatusData[] = (int)$stat['count'];
    }
    // نسبة النجاح (Placement Rate)
    $placementRate = $totalStudents > 0 ? round(($placedCount / $totalStudents) * 100) : 0;
    $stats = [
        'placedCount' => $placedCount,
        'unplacedCount' => $unplacedCount,
        'placementRate' => $placementRate,
        'totalCompanies' => $admin->getPartnerCompanies()->count(),
        'activeAgreements' => count($acceptedApps),
        'pendingApps' => count($pendingApps),
        'totalStudents'   => $admin->getStudents()->count(),
        'placedStudentsCount' => $placedCount, // تأكدي من هذا الاسم
        'placedPercentage' => $placementRate,  // تأكدي من هذا الاسم
        'pendingPartners' => count($pendingVerifications),
        'allRequests'         => $allRequests,
        'appStatusLabels' => $appStatusLabels,
        'appStatusData' => $appStatusData,
    ];


    return $this->render('admin/dashboard.html.twig', [
        'chartLabels' => $chartLabels,
        'chartData'    => $chartData,
        'stats'               => $stats,
        'pendingApps'         => $pendingApps,
        'acceptedApps'        => $acceptedApps,
        'pendingVerifications'=> $pendingVerifications,
        'myStudents'          => $admin->getStudents(),
        'myCompanies'         => $admin->getPartnerCompanies(),
        'allRequests' => $allRequests,
        'statusStats' => $statusData,
    ]);
}

    #[Route('/admin/validations', name: 'app_admin_validations')]
    public function validations(EntityManagerInterface $em): Response
    {
        /** @var User $admin */
        $admin = $this->getUser();

        // نعدل الاستعلام ليجلب فقط الطلبات التي حالتها 'pending_admin'
        // والتي تنتمي لنفس جامعة الأدمن الحالي
        $pendingApps = $em->getRepository(Application::class)
            ->findBy([
                'status' => 'pending_admin',
                'university' => $admin // تأكدي أن علاقة الجامعة مرتبطة بالأدمن أو الطلب
            ]);

        return $this->render('admin/validations.html.twig', [
            'pendingApps' => $pendingApps,
            'university'  => $admin,
        ]);
    }
    #[Route('/admin/applications/{id}/approve', name: 'app_admin_approve_application', methods: ['POST'])]
    public function approveApplication(int $id, EntityManagerInterface $em, ConventionGeneratorService $conventionGenerator): JsonResponse
    {
        $application = $em->getRepository(Application::class)->find($id);
        if (!$application) return $this->json(['error' => 'Introuvable.'], 404);
        if ($application->getStatus() === 'accepted') return $this->json(['error' => 'Déjà acceptée.'], 400);

        $application->setStatus('accepted');
        $application->setApprovedAt(new \DateTimeImmutable());

        /** @var User $admin */
        $admin = $this->getUser();
        try {
            $filename = $conventionGenerator->generate($application, $admin);
            $application->setConventionFile($filename);
        } catch (\Throwable $e) { $filename = null; }
// 3. ─── إضافة المنطق الجديد: تعبئة جدول الـ Internship ───
        $existingInternship = $em->getRepository(Internship::class)->findOneBy(['application' => $application]);

        if (!$existingInternship) {
            $internship = new Internship();
            $internship->setApplication($application);

            // جلب تاريخ البداية من عرض العمل (Offers)
            $offer = $application->getOffer();
            $startDate = $offer->getInternshipStart();

            if (!$startDate) {
                $startDate = new \DateTime(); // تاريخ احتياطي إذا كان فارغاً
            }
            $internship->setStartDate($startDate);

            // حساب تاريخ النهاية بناءً على مدة العرض (Duration)
            $durationInMonths = (int)$offer->getDuration();
            if ($durationInMonths <= 0) {
                $durationInMonths = 3; // افتراضياً 3 أشهر إذا لم تُحدد الشركة المدة
            }

            $endDate = clone $startDate;
            $endDate->modify("+$durationInMonths months");
            $internship->setEndDate($endDate);

            // تعيين الحالة المبدئية وسجل وقت الإنشاء
            $internship->setStatus('pending');


            $em->persist($internship);
        }
        $em->flush();

        return $this->json([
            'success'     => true,
            'downloadUrl' => $filename
                ? $this->generateUrl('app_admin_download_convention', ['id' => $application->getId()])
                : null,
        ]);
    }

    #[Route('/admin/applications/{id}/reject', name: 'app_admin_reject_application', methods: ['POST'])]
    public function rejectApplication(int $id, Request $request, EntityManagerInterface $em): JsonResponse
    {
        $application = $em->getRepository(Application::class)->find($id);
        if (!$application) return $this->json(['error' => 'Introuvable.'], 404);

        $data = json_decode($request->getContent(), true);
        $application->setStatus('rejected');
        $application->setRejectionReason($data['reason'] ?? null);
        $em->flush();
        return $this->json(['success' => true]);
    }

    #[Route('/admin/applications/{id}/convention', name: 'app_admin_download_convention')]
    public function downloadConvention(int $id, EntityManagerInterface $em): Response
    {
        $application = $em->getRepository(Application::class)->find($id);
        if (!$application || !$application->getConventionFile()) throw $this->createNotFoundException();

        $filePath = $this->getParameter('kernel.project_dir') . '/public/conventions/' . $application->getConventionFile();
        if (!file_exists($filePath)) throw $this->createNotFoundException();

        $response = new BinaryFileResponse($filePath);
        $response->setContentDisposition(ResponseHeaderBag::DISPOSITION_ATTACHMENT, 'convention_' . $application->getStudent()->getLastName() . '.docx');
        $response->headers->set('Content-Type', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document');
        return $response;
    }

    // =========================================================================
    // طلبات الانتساب من الشركات
    // =========================================================================

    #[Route('/admin/verification-requests', name: 'app_admin_verification_requests')]
    public function verificationRequests(EntityManagerInterface $em): Response
    {
        /** @var User $admin */
        $admin = $this->getUser();
        $allRequests = $em->getRepository(VerificationRequest::class)->findBy(
            ['university' => $admin], ['createdAt' => 'DESC']
        );
        return $this->render('admin/verification_requests.html.twig', [
            'allRequests' => $allRequests,
            'admin'       => $admin,
        ]);
    }

    /**
     * قبول طلب شراكة شركة
     * ★ هنا يُضاف السجل في جدول company_university تلقائياً
     */
    #[Route('/admin/verification-requests/{id}/accept', name: 'app_admin_accept_verification', methods: ['POST'])]
    public function acceptVerification(int $id, EntityManagerInterface $em): JsonResponse
    {
        $vr = $em->getRepository(VerificationRequest::class)->find($id);

        if (!$vr || $vr->getUniversity() !== $this->getUser()) {
            return $this->json(['error' => 'Accès refusé.'], 403);
        }
        if ($vr->getStatus() !== 'Pending') {
            return $this->json(['error' => 'Déjà traité.'], 400);
        }

        /** @var User $company */
        $company = $vr->getCompany();
        /** @var User $admin */
        $admin = $this->getUser();

        // ★ الخطوة الجوهرية: تسجيل الشراكة في جدول company_university
        $company->addPartnerUniversity($admin);

        $vr->setStatus('Accepted');
        $em->flush();

        return $this->json([
            'success'     => true,
            'companyName' => $company->getCompanyName() ?? $company->getFullName(),
            'companyId'   => $company->getId(),
        ]);
    }

    #[Route('/admin/verification-requests/{id}/reject', name: 'app_admin_reject_verification', methods: ['POST'])]
    public function rejectVerification(int $id, Request $request, EntityManagerInterface $em): JsonResponse
    {
        $vr = $em->getRepository(VerificationRequest::class)->find($id);
        if (!$vr || $vr->getUniversity() !== $this->getUser()) {
            return $this->json(['error' => 'Accès refusé.'], 403);
        }
        // استقبال البيانات القادمة من الجافاسكريبت (JSON)
        $data = json_decode($request->getContent(), true);
        $reason = $data['reason'] ?? 'No reason provided'; // استخراج السبب
        $vr->setStatus('Rejected');
        $vr->setRejectionReason($reason);
        $em->flush();
        return $this->json(['success' => true]);
    }

    // =========================================================================
    // ختم + صفحات أخرى
    // =========================================================================

    #[Route('/admin/stamp', name: 'app_admin_upload_stamp', methods: ['POST'])]
    public function uploadStamp(Request $request, EntityManagerInterface $em): JsonResponse
    {
        /** @var User $admin */
        $admin = $this->getUser();
        $stampFile = $request->files->get('file');
        if (!$stampFile) return $this->json(['error' => 'Aucun fichier reçu.'], 400);

        $allowedMime = ['image/png', 'image/jpeg', 'image/webp'];
        if (!in_array($stampFile->getMimeType(), $allowedMime, true)) {
            return $this->json(['error' => 'Format non supporté.'], 400);
        }

        $stampsDir = $this->getParameter('kernel.project_dir') . '/public/uploads/stamps/';
        if (!is_dir($stampsDir)) mkdir($stampsDir, 0755, true);

        if ($admin->getStampFilename() && file_exists($stampsDir . $admin->getStampFilename())) {
            unlink($stampsDir . $admin->getStampFilename());
        }

        $newFilename = 'stamp_u' . $admin->getId() . '_' . uniqid() . '.' . $stampFile->guessExtension();
        $stampFile->move($stampsDir, $newFilename);
        $admin->setStampFilename($newFilename);
        $em->flush();
        return $this->json(['success' => true, 'url' => '/uploads/stamps/' . $newFilename]);
    }

    #[Route('/admin/agreements', name: 'app_admin_agreements')]
    public function agreements(): Response { return $this->render('admin/agreements.html.twig'); }

    #[Route('/admin/statistics', name: 'app_admin_statistics')]
    public function statistics(): Response { return $this->render('admin/statistics.html.twig'); }

}

