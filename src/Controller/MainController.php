<?php

namespace App\Controller;

// هذه الأسطر ضرورية جداً لإخبار سيمفوني أين يجد الجداول
use App\Entity\Communication;
use App\Entity\ScientificEvent;
use App\Repository\CommunicationRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class MainController extends AbstractController
{
    #[Route('/', name: 'app_home')]
    public function index(): Response
    {
        return $this->render('main/index.html.twig');
    }

    // --- خاص بالباحث (Author) ---
    #[Route('/author/dashboard', name: 'app_author_dashboard')]
    public function authorDashboard(CommunicationRepository $repo): Response
    {
        $comms = $repo->findAll();
        $stats = [
            'total' => count($comms),
            'accepted' => count(array_filter($comms, fn($c) => $c->getStatus() === 'Accepted')),
            'pending' => count(array_filter($comms, fn($c) => $c->getStatus() === 'Pending')),
        ];

        return $this->render('author/author_dashboard.html.twig', [
            'communications' => $comms,
            'stats' => $stats
        ]);
    }

    #[Route('/submit', name: 'app_submit', methods: ['GET', 'POST'])]
    public function submitResearch(Request $request, EntityManagerInterface $em): Response
    {
        if ($request->isMethod('POST')) {
            $communication = new Communication();
            $communication->setTitle($request->request->get('title'));
            $communication->setAbstract($request->request->get('abstract'));
            $communication->setStatus('Pending');

            $user = $this->getUser();
            $communication->setAuthor($user);

            // جلب حدث علمي حقيقي لربط البحث به
            $event = $em->getRepository(ScientificEvent::class)->findOneBy([]);
            $communication->setEvent($event);

            $em->persist($communication);
            $em->flush();

            $this->addFlash('success', 'Research submitted successfully!');
            return $this->redirectToRoute('app_author_dashboard');
        }

        return $this->render('author/submit_research.html.twig');
    }

    // --- خاص بالمنظم (Organizer) ---
    #[Route('/organizer/dashboard', name: 'app_organizer_dashboard')]
    public function organizerDashboard(CommunicationRepository $repo): Response
    {
        return $this->render('organizer/organizer_dashboard.html.twig', [
            'communications' => $repo->findAll()
        ]);
    }

    #[Route('/update-status/{id}/{status}', name: 'app_update_status')]
    public function updateStatus(int $id, string $status, EntityManagerInterface $em): Response
    {
        try {
            $conn = $em->getConnection();
            $sql = "UPDATE communication SET status = :status WHERE id = :id";
            $conn->executeStatement($sql, ['status' => $status, 'id' => $id]);
            $this->addFlash('success', "Status updated successfully!");
        } catch (\Exception $e) {
            $this->addFlash('error', "Error updating status.");
        }
        return $this->redirectToRoute('app_organizer_dashboard');
    }

    // --- خاص بالمراجع (Reviewer) ---
    #[Route('/reviewer/dashboard', name: 'app_reviewer_dashboard')]
    public function reviewerDashboard(CommunicationRepository $repo): Response
    {
        return $this->render('reviewer/reviewer_dashboard.html.twig', [
            'communications' => $repo->findAll()
        ]);
    }
    // --- خاص بالمسؤول الأعلى (Super Admin) ---
    #[Route('/superadmin/dashboard', name: 'app_superadmin_dashboard')]
    public function superAdminDashboard(EntityManagerInterface $em): Response
    {
        $stats = ['total_users' => 150, 'total_events' => 12, 'pending_reviews' => 45];
        return $this->render('super_admin/dashboard.html.twig', ['stats' => $stats]);
    }
    // --- روابط الحساب ---
    #[Route('/login', name: 'app_login')]
    public function login(): Response { return $this->render('security/login.html.twig'); }

    #[Route('/register', name: 'app_register')]
    public function register(): Response { return $this->render('registration/register.html.twig'); }

    #[Route('/workshops', name: 'app_workshops')]
    public function workshops(EntityManagerInterface $em): Response
    {
        $conn = $em->getConnection();
        $sql = "SELECT * FROM workshop";
        $workshops = $conn->fetchAllAssociative($sql);
        return $this->render('workshop/workshop_dashboard.html.twig', ['workshops' => $workshops]);
    }

    // --- عرض تفاصيل الحدث العلمي ---
    #[Route('/event/show/{id}', name: 'app_event_show')]
    public function eventDetail(int $id, EntityManagerInterface $em): Response
    {
        $conn = $em->getConnection();
        $sql = "SELECT * FROM scientific_event WHERE id = :id";
        $event = $conn->fetchAssociative($sql, ['id' => $id]);

        if (!$event) {
            throw $this->createNotFoundException('The event does not exist');
        }

        return $this->render('event/show.html.twig', [
            'event' => $event,
        ]);
    }
    // أضيفي هذه الدالة في MainController.php
    #[Route('/forgot-password', name: 'app_forgot_password')]
    public function forgotPassword(): Response
    {
        return $this->render('security/forgot_password.html.twig');
    }
    // أضف هذه الدالة داخل MainController.php
// src/Controller/MainController.php

    #[Route('/redirect-user', name: 'app_redirect_user')]
    public function redirectUser(): Response
    {
        // التأكد من أن المستخدم سجل دخوله
        $user = $this->getUser();
        if (!$user) {
            return $this->redirectToRoute('app_login');
        }

        // التوجيه بناءً على الرتبة
        if ($this->isGranted('ROLE_ORGANIZER')) {
            return $this->redirectToRoute('app_organizer_dashboard');
        }

        if ($this->isGranted('ROLE_AUTHOR')) {
            return $this->redirectToRoute('app_author_dashboard');
        }

        // إذا لم يملك أي رتبة خاصة يذهب للرئيسية
        return $this->redirectToRoute('app_home');
    }
    // --- عرض ملف تعريف المستخدم (Profile) ---
    #[Route('/profile', name: 'app_profile')]
    public function profile(): Response
    {
        // الحصول على المستخدم المسجل حالياً
        $user = $this->getUser();

        // إذا لم يكن هناك مستخدم مسجل، يتم توجيهه لصفحة الدخول
        if (!$user) {
            return $this->redirectToRoute('app_login');
        }

        return $this->render('author/profile.html.twig', [
            'user' => $user,
        ]);
    }

// --- عرض صفحة تعديل البيانات (Edit Profile) ---
    #[Route('/profile/edit', name: 'app_profile_edit')]
    public function editProfile(): Response
    {
        $user = $this->getUser();

        if (!$user) {
            return $this->redirectToRoute('app_login');
        }

        return $this->render('author/edit.html.twig', [
            'user' => $user,
        ]);
    }
}