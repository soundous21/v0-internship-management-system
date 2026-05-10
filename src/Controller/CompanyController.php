<?php

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
        $company = $this->getUser();
        // التصحيح: استعمل $company وليس $user
        if (!$company->isApprovedByWebmaster()) {
            return $this->render('company/pending_approval.html.twig');
        }
        $offersRepo = $em->getRepository(Offers::class);
        $allOffers = $offersRepo->findBy(['company' => $company]);

        $now = new \DateTime();
        $next48Hours = (new \DateTime())->modify('+48 hours');
        $nextWeek = (new \DateTime())->modify('+7 days');

        $expiringSoon = [];
        $startingSoon = [];
        $noApplicants = [];
        $internshipStarted = [];

        $pendingCount = 0;
        $acceptedCount = 0;
        $refusedCount = 0;

        foreach ($allOffers as $offer) {


            $today = new \DateTime();

// التحقق هل يوجد طلاب مقبولون نهائيا
            $hasAcceptedStudents = false;

            foreach ($offer->getApplications() as $application) {
                if ($application->getStatus() === 'accepted') {
                    $hasAcceptedStudents = true;
                    break;
                }
            }

// إذا بدأ تاريخ التربص
            if ($offer->getInternshipStart() && $offer->getInternshipStart() <= $today) {

                // يوجد طلاب مقبولون => Active
                if ($hasAcceptedStudents) {
                    $offer->setStatus('Active');
                }
                // لا يوجد متدربون => Inactive
                else {
                    $offer->setStatus('Inactive');
                }
            }


            $applications = $offer->getApplications();
            $appCount = $applications ? $applications->count() : 0;

            foreach ($applications as $app) {
                if ($app->getStatus() === 'pending') $pendingCount++;
                elseif ($app->getStatus() === 'accepted') $acceptedCount++;
                elseif (in_array($app->getStatus(), ['refused', 'rejected'])) $refusedCount++;
            }

            if ($offer->getDeadline() && $offer->getDeadline() > $now && $offer->getDeadline() <= $next48Hours) {
                $expiringSoon[] = $offer;
            }

            if ($offer->getInternshipStart() && $offer->getInternshipStart() > $now && $offer->getInternshipStart() <= $nextWeek) {
                $startingSoon[] = $offer;
            }

            if ($appCount === 0) {
                $noApplicants[] = $offer;
            }

            if ($offer->getInternshipStart() && $offer->getInternshipStart() <= $now) {
                $internshipStarted[] = $offer;
            }
        }
        $em->flush();
        return $this->render('company/dashboard.html.twig', [
            'company'           => $company,
            'total_offers'      => count($allOffers),
            'active_offers'     => count($offersRepo->findBy(['company' => $company, 'status' => 'Active'])),
            'expiringSoon'      => $expiringSoon,
            'startingSoon'      => $startingSoon,
            'noApplicants'      => $noApplicants,
            'internshipStarted' => $internshipStarted,
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
                $originalFilename = pathinfo($logoFile->getClientOriginalName(), PATHINFO_FILENAME);
                $safeFilename = $slugger->slug($originalFilename);
                $newFilename = $safeFilename . '-' . uniqid() . '.' . $logoFile->guessExtension();
                $logoFile->move($this->getParameter('logos_directory'), $newFilename);
                if (method_exists($company, 'setLogo')) {
                    $company->setLogo($newFilename);
                }
            }

            $vFile = $request->files->get('verificationFile');
            if ($vFile) {
                $originalFilename = pathinfo($vFile->getClientOriginalName(), PATHINFO_FILENAME);
                $safeFilename = $slugger->slug($originalFilename);
                $newFilename = $safeFilename . '-' . uniqid() . '.' . $vFile->guessExtension();
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

// في ملف CompanyController.php

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
        $company = $this->getUser();

        // 1. جلب كل العروض الخاصة بهذه الشركة أولاً
        $myOffers = $em->getRepository(Offers::class)->findBy(['company' => $company]);

        // 2. جلب الطلبات الخاصة بهذه العروض فقط والتي لم تُرفض
        $candidates = $repository->findBy([
            'offer' => $myOffers,
            'status' => ['pending', 'accepted', 'pending_admin']
        ]);

        return $this->render('company/dashboard.html.twig', [
            'company'    => $company,
            'candidates' => $candidates,
            'current_route' => 'app_company_applicants' // تأكد من إرسال الرووت لتفعيل الشرط في Twig
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
            // في createOffer — بعد setLevel()
            $offer->setSeats($request->request->get('seats') ? (int)$request->request->get('seats') : null);

            $offer->setLevel($request->request->get('level')); // تأكد من وجود setLevel في Entity
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

            $skillsRaw = $request->request->get('skills');
            if ($skillsRaw) {
                $skillNames = array_map('trim', explode(',', $skillsRaw));
                foreach ($skillNames as $skillName) {
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



            // إضافة استقبال عدد المقاعد
            $seats = $request->request->get('seats');
            if ($seats !== null) {
                $offer->setSeats((int)$seats);
            }


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
// داخل الدالة المسؤولة عن حفظ العرض (New or Edit)
// في createOffer — بعد setLevel()
            $offer->setSeats($request->request->get('seats') ? (int)$request->request->get('seats') : null);

// أضف هذه الأسطر لكي يتم حفظ القيم الجديدة
            $offer->setLocationType($request->request->get('locationType'));
            $offer->setLevel($request->request->get('level'));
            if ($request->request->get('startDate')) $offer->setStartDate(new \DateTime($request->request->get('startDate')));
            if ($request->request->get('internshipStart')) $offer->setInternshipStart(new \DateTime($request->request->get('internshipStart')));
            if ($request->request->get('deadline')) $offer->setDeadline(new \DateTime($request->request->get('deadline')));

            foreach ($offer->getSkills() as $skill) $offer->removeSkill($skill);

            $skillsRaw = $request->request->get('skills');
            if ($skillsRaw) {
                $skillNames = array_map('trim', explode(',', $skillsRaw));
                foreach ($skillNames as $skillName) {
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


            // تحديث عدد المقاعد
            $seats = $request->request->get('seats');
            if ($seats !== null) {
                $offer->setSeats((int)$seats);
            }

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

    // src/Controller/CompanyController.php

    #[Route('/company/offer/{id}/candidates', name: 'app_company_offer_candidates')]
    public function viewCandidates(int $id, EntityManagerInterface $em): Response
    {
        $offer = $em->getRepository(Offers::class)->find($id);

        // التحقق من وجود العرض ومن ملكيته للشركة الحالية
        if (!$offer || $offer->getCompany() !== $this->getUser()) {
            throw $this->createNotFoundException('Offer not found.');
        }

        $candidates = $em->getRepository(Application::class)->findBy(['offer' => $offer]);

        return $this->render('company/candidates_list.html.twig', [
            'offer'         => $offer,
            'offers'        => [$offer],       // الحل: تمرير العرض داخل مصفوفة باسم 'offers'
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
        $company = $this->getUser();
        $universityId = $request->request->get('university_id');
        $university = $em->getRepository(User::class)->find($universityId);

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
        $company = $this->getUser();
        $universities = $em->getRepository(User::class)->findByRole('ROLE_ADMIN');
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
    // ★ VALIDATE: الشركة تقبل الطالب → يُرسل للأدمين (pending_admin)
    //   لا تُنشأ الاتفاقية هنا — الأدمين هو من يُنشئها.
    // ═══════════════════════════════════════════════════════════════

    #[Route('/company/application/{id}/validate', name: 'app_company_application_validate', methods: ['POST'])]
    public function validateApplication(int $id, EntityManagerInterface $em): Response
    {
        $application = $em->getRepository(Application::class)->find($id);

        if (!$application || $application->getOffer()->getCompany() !== $this->getUser()) {
            throw $this->createNotFoundException('الطلب غير موجود.');
        }

        // ★ الشركة تقبل الطالب → status = pending_admin (في انتظار الأدمين)
        // الاتفاقية لا تُنشأ هنا، بل عندما يضغط الأدمين على Validate.
        $application->setStatus('pending_admin');

        $em->flush();

        $this->addFlash('success', 'تم إرسال الطلب إلى الجامعة للمراجعة والموافقة النهائية.');
        return $this->redirectToRoute('app_company_offer_candidates', ['id' => $application->getOffer()->getId()]);
    }

    // ═══════════════════════════════════════════════════════════════
    // ★ REFUSE: الشركة ترفض الطالب مع سبب → يراه الطالب في صفحته
    // ═══════════════════════════════════════════════════════════════

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

        // ★ status = refused  +  rejectionReason محفوظ → يظهر للطالب في قسم "My Applications"
        $application->setStatus('refused');
        $application->setRejectionReason($reason);
        $em->flush();

// إذا كان الطلب AJAX → أرجع JSON فقط بدون redirect
        if ($request->isXmlHttpRequest()) {
            return new JsonResponse(['success' => true, 'message' => 'Candidature refusée.']);
        }

// إذا كان طلب عادي → redirect كالسابق (fallback)
        $this->addFlash('error', 'تم رفض الطلب...');

        return $this->redirectToRoute('app_company_offer_candidates', ['id' => $application->getOffer()->getId()]);

    }
    // ═══════════════════════════════════════════════════════════════
    // Helpers
    // ═══════════════════════════════════════════════════════════════

    private function calculateMatch(Application $app): int
    {
        $studentSkills = $app->getStudent()->getSkills()->map(fn($s) => $s->getIdTag())->toArray();
        $offerSkills   = $app->getOffer()->getSkills()->map(fn($s) => $s->getIdTag())->toArray();

        if (empty($offerSkills)) return 0;

        $matches = array_intersect($studentSkills, $offerSkills);
        return (int)((count($matches) / count($offerSkills)) * 100);
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
        $request = $em->getRepository(VerificationRequest::class)->find($id);

        // التأكد من أن الطلب موجود وأن الشركة الحالية هي صاحبة الطلب
        if (!$request || $request->getCompany() !== $this->getUser()) {
            return new JsonResponse(['success' => false, 'message' => 'الطلب غير موجود أو لا تملك صلاحية حذفه.'], 404);
        }

        try {
            $em->remove($request);
            $em->flush();
            return new JsonResponse(['success' => true, 'message' => 'تم حذف الطلب من السجل بنجاح.']);
        } catch (\Exception $e) {
            return new JsonResponse(['success' => false, 'message' => 'حدث خطأ أثناء الحذف.'], 500);
        }
    }









    // داخل CompanyController.php
    #[Route('/application/{id}/reject', name: 'app_reject_application', methods: ['POST'])]
    public function rejectApplication(
        Application $application,
        Request $request,
        EntityManagerInterface $em
    ): JsonResponse
    {
        $reason = $request->request->get('reason');

        $application->setStatus('refused');
        $application->setRejectionReason($reason);

        $em->flush();

        return new JsonResponse([
            'success' => true,
            'applicationId' => $application->getId(),
        ]);
    }





    // داخل CompanyController.php

    #[Route('/company/application/{id}/download-convention', name: 'app_company_download_convention')]
    public function downloadConvention(Application $application)
    {
        // التأكد من أن الشركة هي صاحبة العرض لضمان الأمان
        if ($application->getOffer()->getCompany() !== $this->getUser()) {
            throw $this->createAccessDeniedException('لا تملك صلاحية الوصول لهذه الاتفاقية.');
        }
        $fileName = $application->getConventionFile(); // هذا هو الاسم الصحيح الموجود في الكينونة
        // استبدله بهذا السطر:
        $filePath = $this->getParameter('kernel.project_dir') . '/public/conventions/' . $fileName;
        if (!file_exists($filePath)) {
            throw $this->createNotFoundException('الملف غير موجود.');
        }

        return $this->file($filePath);
    }
}