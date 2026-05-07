<?php
// src/Controller/AdminDashboardController.php
namespace App\Controller;

use App\Entity\Application;
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
{
    #[Route('/admin/dashboard', name: 'app_admin_dashboard')]
    public function dashboard(EntityManagerInterface $em): Response
    {
        /** @var User $admin */
        $admin = $this->getUser();

        // --- 1. بيانات المنحنى (Trends) - آخر 6 أشهر ---
        $chartLabels = [];
        $chartData = [];
        for ($i = 5; $i >= 0; $i--) {
            $date = (new \DateTime())->modify("-$i months");
            $chartLabels[] = $date->format('M');

            $count = $em->getRepository(Application::class)->createQueryBuilder('a')
                ->select('count(a.id)')
                ->join('a.student', 's')
                ->where('s.universityEntity = :admin')
                ->andWhere('a.createdAt LIKE :date')
                ->setParameter('admin', $admin)
                ->setParameter('date', $date->format('Y-m') . '%')
                ->getQuery()->getSingleScalarResult();
            $chartData[] = (int)$count;
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

        // 2. Fetch accepted applications (The missing part)
// استبدل الكود القديم بهذا الكود:
        $acceptedApps = $em->getRepository(Application::class)
            ->createQueryBuilder('a')
            ->join('a.student', 's')
            ->where('s.universityEntity = :admin') // نستخدم universityEntity المعرف في كيان User
            ->andWhere('a.status = :status')
            ->setParameter('admin', $admin)
            ->setParameter('status', 'accepted')
            ->getQuery()
            ->getResult();
        //  $acceptedApps = $em->getRepository(Application::class)->findBy([
        //    'university' => $admin,
        //  'status' => 'accepted'
        // ]);

        // جلب كافة طلبات التحقق لتمريرها للمتغير allRequests
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
        $stats = [
            'totalStudents'   => $admin->getStudents()->count(),
            'totalCompanies'  => $admin->getPartnerCompanies()->count(),
            'activeAgreements'=> $em->getRepository(Application::class)->count(['status' => 'accepted']),
            'pendingApps'     => count($pendingApps),
            'activeAgreements'=> count($acceptedApps),
            'pendingPartners' => count($pendingVerifications),
            'allRequests'         => $allRequests,

        ];

        return $this->render('admin/dashboard.html.twig', [
            'chartLabels' => $chartLabels,
            'chartData'    => $chartData,
            'statusStats'  => $statusStats,

            'admin'               => $admin,
            'stats'               => $stats,
            'pendingApps'         => $pendingApps,
            'acceptedApps'        => $acceptedApps,
            'pendingVerifications'=> $pendingVerifications,
            'myStudents'          => $admin->getStudents(),
            'myCompanies'         => $admin->getPartnerCompanies(),
            'allRequests' => $allRequests,
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

