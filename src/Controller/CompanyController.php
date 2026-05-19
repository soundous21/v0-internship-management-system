<?php
// ══════════════════════════════════════════════════════════════════
// ملاحظة: الجزء الوحيد الذي تغيّر هو دالة index() (Dashboard).
// بقية الدوال لم تُمس.
// ══════════════════════════════════════════════════════════════════

namespace App\Controller;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\String\Slugger\SluggerInterface;
use App\Entity\Offers;
use App\Entity\Skills;
use App\Entity\VerificationRequest;
use App\Entity\Application;
use App\Repository\ApplicationRepository;

#[IsGranted('ROLE_COMPANY')]
class CompanyController extends AbstractController
{
    // ═══════════════════════════════════════════════════════════════
    // Dashboard
    // ═══════════════════════════════════════════════════════════════

    #[Route('/company/dashboard', name: 'app_company_dashboard')]
    public function index(EntityManagerInterface $em): Response
    {
        /** @var User $company */
        $company = $this->getUser();

        if (!$company->isApprovedByWebmaster()) {
            return $this->render('company/pending_approval.html.twig');
        }

        $allOffers = $em->getRepository(Offers::class)->findBy(['company' => $company]);

        $now         = new \DateTime();
        $next48Hours = (new \DateTime())->modify('+48 hours');
        $nextWeek    = (new \DateTime())->modify('+7 days');

        $expiringSoon      = [];
        $startingSoon      = [];
        $noApplicants      = [];
        $internshipStarted = [];

        $pendingCount  = 0;
        $acceptedCount = 0;
        $refusedCount  = 0;

        foreach ($allOffers as $offer) {

            // ✅ لا نعدّل status هنا أبداً — getComputedStatus() تحسب الحالة
            //    بدون لمس قاعدة البيانات.

            $applications = $offer->getApplications();
            $appCount     = $applications->count();

            foreach ($applications as $app) {
                $s = $app->getStatus();
                if ($s === 'pending')                          $pendingCount++;
                elseif ($s === 'accepted')                     $acceptedCount++;
                elseif (in_array($s, ['refused', 'rejected'])) $refusedCount++;
            }

            // تنبيهات الـ deadline (خلال 48 ساعة)
            if ($offer->getDeadline() && $offer->getDeadline() > $now && $offer->getDeadline() <= $next48Hours) {
                $expiringSoon[] = $offer;
            }

            // تنبيهات بداية التربص (خلال أسبوع)
            if ($offer->getInternshipStart() && $offer->getInternshipStart() > $now && $offer->getInternshipStart() <= $nextWeek) {
                $startingSoon[] = $offer;
            }

            // عروض بدون متقدمين
            if ($appCount === 0) {
                $noApplicants[] = $offer;
            }

            // عروض بدأ تربصها
            if ($offer->getInternshipStart() && $offer->getInternshipStart() <= $now) {
                $internshipStarted[] = $offer;
            }
        }

        // ✅ لا يوجد $em->flush() هنا — الداشبورد للقراءة فقط

        // عدد العروض "Active" الحقيقية (بالمنطق المحسوب)
        $activeOffersCount = count(array_filter($allOffers, fn($o) => $o->getComputedStatus() === 'Active'));

        return $this->render('company/dashboard.html.twig', [
            'company'             => $company,
            'offers'              => $allOffers,
            'total_offers'        => count($allOffers),
            'active_offers'       => $activeOffersCount,
            'expiringSoon'        => $expiringSoon,
            'startingSoon'        => $startingSoon,
            'noApplicants'        => $noApplicants,
            'internshipStarted'   => $internshipStarted,
            'pending_applicants'  => $pendingCount,
            'accepted_applicants' => $acceptedCount,
            'refused_applicants'  => $refusedCount,
            'total_applicants'    => $pendingCount + $acceptedCount + $refusedCount,
        ]);
    }

    // ═══════════════════════════════════════════════════════════════
    // Profile
    // ═══════════════════════════════════════════════════════════════

    #[Route('/company/profile/update', name: 'app_company_profile_update', methods: ['POST'])]
    public function updateProfile(Request $request, EntityManagerInterface $entityManager, SluggerInterface $slugger): JsonResponse
    {
        /** @var User $company */
        $company = $this->getUser();

        try {
            $company->setCompanyName($request->request->get('companyName'));
            $company->setEmail($request->request->get('email'));
            $company->setPhone($request->request->get('phone'));
            $company->setIndustry($request->request->get('industry'));
            $company->setWilaya($request->request->get('wilaya'));
            $company->setWebsite($request->request->get('website'));
            $company->setBio($request->request->get('bio'));
            $company->setLatitude($request->request->get('latitude'));
            $company->setLongitude($request->request->get('longitude'));

            $logoFile = $request->files->get('logo');
            if ($logoFile) {
                $safeFilename = $slugger->slug(pathinfo($logoFile->getClientOriginalName(), PATHINFO_FILENAME));
                $newFilename  = $safeFilename . '-' . uniqid() . '.' . $logoFile->guessExtension();
                $logoFile->move($this->getParameter('logos_directory'), $newFilename);
                if (method_exists($company, 'setLogo')) {
                    $company->setLogo($newFilename);
                }
            }

            $vFile = $request->files->get('verificationFile');
            if ($vFile) {
                $safeFilename = $slugger->slug(pathinfo($vFile->getClientOriginalName(), PATHINFO_FILENAME));
                $newFilename  = $safeFilename . '-' . uniqid() . '.' . $vFile->guessExtension();
                $vFile->move($this->getParameter('verification_directory'), $newFilename);
                $company->setVerificationFile($newFilename);
            }

            $entityManager->flush();
            return new JsonResponse(['status' => 'success', 'message' => 'Saved successfully!']);
        } catch (\Exception $e) {
            return new JsonResponse(['status' => 'error', 'message' => 'Error: ' . $e->getMessage()], 500);
        }
    }

    #[Route('/company/profile', name: 'app_company_profile')]
    public function profile(): Response
    {
        return $this->render('company/dashboard.html.twig', [
            'company' => $this->getUser(),
        ]);
    }

    // ═══════════════════════════════════════════════════════════════
    // Offers
    // ═══════════════════════════════════════════════════════════════

    #[Route('/company/offers', name: 'app_company_offers')]
    public function manageOffers(EntityManagerInterface $em): Response
    {
        $company = $this->getUser();
        $offers  = $em->getRepository(Offers::class)
            ->findBy(['company' => $company], ['createdAt' => 'DESC']);

        return $this->render('company/dashboard.html.twig', [
            'company' => $company,
            'offers'  => $offers,
        ]);
    }

    #[Route('/company/applicants', name: 'app_company_applicants')]
    public function applicants(ApplicationRepository $repository, EntityManagerInterface $em): Response
    {
        $company  = $this->getUser();
        $myOffers = $em->getRepository(Offers::class)->findBy(['company' => $company]);

        $candidates = $repository->findBy([
            'offer'  => $myOffers,
            'status' => ['pending', 'accepted', 'pending_admin'],
        ]);

        return $this->render('company/dashboard.html.twig', [
            'company'       => $company,
            'candidates'    => $candidates,
            'current_route' => 'app_company_applicants',
        ]);
    }

    #[Route('/company/offers/new', name: 'app_company_offers_new', methods: ['GET', 'POST'])]
    public function createOffer(Request $request, EntityManagerInterface $em): Response
    {
        if ($request->isMethod('POST')) {
            $offer = new Offers();
            $offer->setTitle($request->request->get('title'));
            $offer->setDescription($request->request->get('description'));
            $offer->setWilaya($request->request->get('wilaya'));
            $offer->setDuration($request->request->get('duration'));
            $offer->setLatitude($request->request->get('latitude'));
            $offer->setLongitude($request->request->get('longitude'));
            $offer->setLocationType($request->request->get('locationType'));
            $offer->setLevel($request->request->get('level'));
            $offer->setSeats($request->request->get('seats') ? (int) $request->request->get('seats') : null);

            if ($request->request->get('deadline')) {
                $offer->setDeadline(new \DateTime($request->request->get('deadline')));
            }
            if ($request->request->get('startDate')) {
                $offer->setStartDate(new \DateTime($request->request->get('startDate')));
            }
            if ($request->request->get('internshipStart')) {
                $offer->setInternshipStart(new \DateTime($request->request->get('internshipStart')));
            }

            $offer->setCompany($this->getUser());
            $this->handleSkills($request, $offer, $em);

            $em->persist($offer);
            $em->flush();

            $this->addFlash('success', 'Offer published successfully!');
            return $this->redirectToRoute('app_company_offers');
        }

        return $this->render('company/dashboard.html.twig', [
            'current_route' => 'app_company_offers_new',
            'company'       => $this->getUser(),
        ]);
    }

    #[Route('/company/offer/delete/{id}', name: 'app_company_offer_delete', methods: ['POST'])]
    public function deleteOffer(int $id, EntityManagerInterface $em): Response
    {
        $offer = $em->getRepository(Offers::class)->find($id);
        if ($offer && $offer->getCompany() === $this->getUser()) {
            $em->remove($offer);
            $em->flush();
            $this->addFlash('success', 'Offer deleted successfully!');
        }
        return $this->redirectToRoute('app_company_offers');
    }

    #[Route('/company/offer/edit/{id}', name: 'app_company_offer_manage', methods: ['GET', 'POST'])]
    public function editOffer(int $id, Request $request, EntityManagerInterface $em): Response
    {
        $offer = $em->getRepository(Offers::class)->find($id);
        if (!$offer || $offer->getCompany() !== $this->getUser()) {
            throw $this->createNotFoundException('Offer not found.');
        }

        if ($request->isMethod('POST')) {
            $offer->setTitle($request->request->get('title'));
            $offer->setDescription($request->request->get('description'));
            $offer->setWilaya($request->request->get('wilaya'));
            $offer->setDuration($request->request->get('duration'));
            $offer->setLatitude($request->request->get('latitude'));
            $offer->setLongitude($request->request->get('longitude'));
            $offer->setLocationType($request->request->get('locationType'));
            $offer->setLevel($request->request->get('level'));
            $offer->setSeats($request->request->get('seats') ? (int) $request->request->get('seats') : null);

            if ($request->request->get('startDate')) {
                $offer->setStartDate(new \DateTime($request->request->get('startDate')));
            }
            if ($request->request->get('internshipStart')) {
                $offer->setInternshipStart(new \DateTime($request->request->get('internshipStart')));
            }
            if ($request->request->get('deadline')) {
                $offer->setDeadline(new \DateTime($request->request->get('deadline')));
            }

            foreach ($offer->getSkills() as $skill) {
                $offer->removeSkill($skill);
            }
            $this->handleSkills($request, $offer, $em);

            $em->flush();
            $this->addFlash('success', 'Offer updated successfully!');
            return $this->redirectToRoute('app_company_offers');
        }

        return $this->render('company/dashboard.html.twig', [
            'current_route' => 'app_company_offer_manage',
            'offer'         => $offer,
            'company'       => $this->getUser(),
        ]);
    }

    #[Route('/company/offer/{id}/candidates', name: 'app_company_offer_candidates')]
    public function viewCandidates(int $id, EntityManagerInterface $em): Response
    {
        $offer = $em->getRepository(Offers::class)->find($id);

        if (!$offer || $offer->getCompany() !== $this->getUser()) {
            throw $this->createNotFoundException('Offer not found.');
        }

        $candidates = $em->getRepository(Application::class)->findBy(['offer' => $offer]);

        return $this->render('company/candidates_list.html.twig', [
            'offer'         => $offer,
            'offers'        => [$offer],
            'candidates'    => $candidates,
            'current_route' => 'app_company_dashboard',
        ]);
    }

    // ═══════════════════════════════════════════════════════════════
    // University Requests
    // ═══════════════════════════════════════════════════════════════

    #[Route('/company/university-requests/send', name: 'app_company_send_verification_request', methods: ['POST'])]
    public function sendVerificationRequest(Request $request, EntityManagerInterface $em): Response
    {
        $company      = $this->getUser();
        $universityId = $request->request->get('university_id');
        $university   = $em->getRepository(User::class)->find($universityId);

        if (!$university) {
            $this->addFlash('error', 'الجامعة غير موجودة.');
            return $this->redirectToRoute('app_company_university_requests');
        }

        $verificationRequest = new VerificationRequest();
        $verificationRequest->setCompany($company);
        $verificationRequest->setUniversity($university);
        $verificationRequest->setStatus('Pending');
        $verificationRequest->setCreatedAt(new \DateTime());

        $em->persist($verificationRequest);
        $em->flush();

        $this->addFlash('success', 'تم إرسال طلب التحقق إلى ' . $university->getUniversityName() . ' بنجاح!');
        return $this->redirectToRoute('app_company_university_requests');
    }

    #[Route('/company/university-requests', name: 'app_company_university_requests')]
    public function universityRequests(EntityManagerInterface $em): Response
    {
        $company            = $this->getUser();
        $universities       = $em->getRepository(User::class)->findByRole('ROLE_ADMIN');
        $universityRequests = $em->getRepository(VerificationRequest::class)->findBy(['company' => $company]);

        return $this->render('company/dashboard.html.twig', [
            'company'             => $company,
            'universities'        => $universities,
            'university_requests' => $universityRequests,
            'total_offers'        => 0,
            'active_offers'       => 0,
            'expiringSoon'        => [],
            'startingSoon'        => [],
            'noApplicants'        => [],
            'internshipStarted'   => [],
            'pending_applicants'  => 0,
            'accepted_applicants' => 0,
            'refused_applicants'  => 0,
            'total_applicants'    => 0,
        ]);
    }

    // ═══════════════════════════════════════════════════════════════
    // Application Actions
    // ═══════════════════════════════════════════════════════════════

    #[Route('/company/application/{id}/validate', name: 'app_company_application_validate', methods: ['POST'])]
    public function validateApplication(int $id, EntityManagerInterface $em): Response
    {
        $application = $em->getRepository(Application::class)->find($id);

        if (!$application || $application->getOffer()->getCompany() !== $this->getUser()) {
            throw $this->createNotFoundException('الطلب غير موجود.');
        }

        $application->setStatus('pending_admin');
        $em->flush();

        $this->addFlash('success', 'تم إرسال الطلب إلى الجامعة للمراجعة والموافقة النهائية.');
        return $this->redirectToRoute('app_company_offer_candidates', ['id' => $application->getOffer()->getId()]);
    }

    #[Route('/company/application/{id}/refuse', name: 'app_company_application_refuse', methods: ['POST'])]
    public function refuseApplication(int $id, Request $request, EntityManagerInterface $em): Response
    {
        $application = $em->getRepository(Application::class)->find($id);

        if (!$application) {
            throw $this->createNotFoundException();
        }
        if ($application->getOffer()->getCompany() !== $this->getUser()) {
            throw $this->createAccessDeniedException();
        }

        $reason = $request->request->get('rejection_reason') ?? 'No reason provided.';
        $application->setStatus('refused');
        $application->setRejectionReason($reason);
        $em->flush();

        if ($request->isXmlHttpRequest()) {
            return new JsonResponse(['success' => true, 'message' => 'Candidature refusée.']);
        }

        $this->addFlash('error', 'تم رفض الطلب.');
        return $this->redirectToRoute('app_company_offer_candidates', ['id' => $application->getOffer()->getId()]);
    }

    #[Route('/application/{id}/reject', name: 'app_reject_application', methods: ['POST'])]
    public function rejectApplication(Application $application, Request $request, EntityManagerInterface $em): JsonResponse
    {
        $application->setStatus('refused');
        $application->setRejectionReason($request->request->get('reason'));
        $em->flush();

        return new JsonResponse(['success' => true, 'applicationId' => $application->getId()]);
    }

    #[Route('/company/application/{id}/download-convention', name: 'app_company_download_convention')]
    public function downloadConvention(Application $application): Response
    {
        if ($application->getOffer()->getCompany() !== $this->getUser()) {
            throw $this->createAccessDeniedException('لا تملك صلاحية الوصول لهذه الاتفاقية.');
        }

        $filePath = $this->getParameter('kernel.project_dir')
            . '/public/conventions/' . $application->getConventionFile();

        if (!file_exists($filePath)) {
            throw $this->createNotFoundException('الملف غير موجود.');
        }

        return $this->file($filePath);
    }

    // ═══════════════════════════════════════════════════════════════
    // Helpers
    // ═══════════════════════════════════════════════════════════════

    private function handleSkills(Request $request, Offers $offer, EntityManagerInterface $em): void
    {
        $skillsRaw = $request->request->get('skills');
        if (!$skillsRaw) {
            return;
        }

        foreach (array_map('trim', explode(',', $skillsRaw)) as $skillName) {
            if (empty($skillName)) continue;

            $skill = $em->getRepository(Skills::class)->findOneBy(['tagName' => $skillName]);
            if (!$skill) {
                $skill = new Skills();
                $skill->setTagName($skillName);
                $skill->setIdTag(0);
                $em->persist($skill);
            }
            $offer->addSkill($skill);
        }
    }

    private function calculateMatch(Application $app): int
    {
        $studentSkills = $app->getStudent()->getSkills()->map(fn($s) => $s->getIdTag())->toArray();
        $offerSkills   = $app->getOffer()->getSkills()->map(fn($s) => $s->getIdTag())->toArray();

        if (empty($offerSkills)) return 0;

        return (int) ((count(array_intersect($studentSkills, $offerSkills)) / count($offerSkills)) * 100);
    }

    #[Route('/company/student/{id}/profile', name: 'app_student_profile_view')]
    public function viewStudentProfile(int $id, EntityManagerInterface $em): Response
    {
        $student = $em->getRepository(User::class)->find($id);
        if (!$student || !in_array('ROLE_STUDENT', $student->getRoles())) {
            throw $this->createNotFoundException('Student not found.');
        }

        return $this->render('company/student_profile.html.twig', [
            'student' => $student,
            'company' => $this->getUser(),
        ]);
    }

    #[Route('/company/university-request/delete/{id}', name: 'app_company_delete_university_request', methods: ['DELETE'])]
    public function deleteUniversityRequest(int $id, EntityManagerInterface $em): JsonResponse
    {
        $verRequest = $em->getRepository(VerificationRequest::class)->find($id);

        if (!$verRequest || $verRequest->getCompany() !== $this->getUser()) {
            return new JsonResponse(['success' => false, 'message' => 'الطلب غير موجود أو لا تملك صلاحية حذفه.'], 404);
        }

        try {
            $em->remove($verRequest);
            $em->flush();
            return new JsonResponse(['success' => true, 'message' => 'تم حذف الطلب من السجل بنجاح.']);
        } catch (\Exception $e) {
            return new JsonResponse(['success' => false, 'message' => 'حدث خطأ أثناء الحذف.'], 500);
        }
    }





// ══════════════════════════════════════════════════════════════════
// أضف هذا الـ Route داخل كلاس CompanyController
// بعد دالة updateProfile() مباشرةً
// ══════════════════════════════════════════════════════════════════

    /**
     * رفع ختم الشركة (Stamp) لتُطبع في وثيقة الاتفاقية
     */
    #[Route('/company/stamp', name: 'app_company_upload_stamp', methods: ['POST'])]
    public function uploadStamp(Request $request, EntityManagerInterface $em): JsonResponse
    {
        /** @var User $company */
        $company = $this->getUser();
        $stampFile = $request->files->get('file');

        // ─── التحقق من وجود الملف ────────────────────────────────
        if (!$stampFile) {
            return $this->json(['error' => 'No file received.'], 400);
        }

        // ─── التحقق من نوع الملف ─────────────────────────────────
        $allowedMime = ['image/png', 'image/jpeg', 'image/webp'];
        if (!in_array($stampFile->getMimeType(), $allowedMime, true)) {
            return $this->json(['error' => 'Unsupported format. Use PNG, JPG, or WebP.'], 400);
        }

        // ─── حجم الملف (2 MB max) ────────────────────────────────
        if ($stampFile->getSize() > 2 * 1024 * 1024) {
            return $this->json(['error' => 'File too large. Maximum 2 MB.'], 400);
        }

        // ─── إنشاء المجلد إذا لم يكن موجوداً ───────────────────
        $stampsDir = $this->getParameter('kernel.project_dir') . '/public/uploads/stamps/';
        if (!is_dir($stampsDir)) {
            mkdir($stampsDir, 0755, true);
        }

        // ─── حذف الختم القديم إن وُجد ───────────────────────────
        if ($company->getStampFilename() && file_exists($stampsDir . $company->getStampFilename())) {
            unlink($stampsDir . $company->getStampFilename());
        }

        // ─── حفظ الملف الجديد ────────────────────────────────────
        $newFilename = 'stamp_c' . $company->getId() . '_' . uniqid() . '.' . $stampFile->guessExtension();
        $stampFile->move($stampsDir, $newFilename);

        $company->setStampFilename($newFilename);
        $em->flush();

        return $this->json([
            'success' => true,
            'url' => '/uploads/stamps/' . $newFilename,
        ]);
    }
}