<?php

namespace App\Controller;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use App\Entity\University;
use App\Entity\Department;


#[IsGranted('ROLE_WEBMASTER')]
#[Route('/webmaster')]
class WebmasterController extends AbstractController
{
    #[Route('/dashboard', name: 'app_webmaster_dashboard')]
    public function index(EntityManagerInterface $em): Response
    {
        $pendingCompanies = $em->getRepository(User::class)->createQueryBuilder('u')
            ->where('u.roles LIKE :role')
            ->andWhere('u.isApprovedByWebmaster = :status')
            ->setParameter('role', '%"ROLE_COMPANY"%')
            ->setParameter('status', false)
            ->getQuery()
            ->getResult();

        // جلب كل الأدمنة الموجودين
        $admins = $em->getRepository(User::class)->createQueryBuilder('u')
            ->where('u.roles LIKE :role')
            ->setParameter('role', '%"ROLE_ADMIN"%')
            ->getQuery()
            ->getResult();
// جلب كل الجامعات المسجلة
        $universities = $em->getRepository(University::class)->findAll();
        return $this->render('webmaster/dashboard.html.twig', [
            'companies' => $pendingCompanies,
            'admins' => $admins,
            'universities' => $universities,
        ]);
    }

    // ✅ موافقة على الشركة
    #[Route('/approve/{id}', name: 'app_webmaster_approve_company', methods: ['POST'])]
    public function approve(User $company, EntityManagerInterface $em): Response
    {
        $company->setIsApprovedByWebmaster(true);
        $em->flush();

        $this->addFlash('success', 'تم تفعيل حساب الشركة بنجاح.');
        return $this->redirectToRoute('app_webmaster_dashboard');
    }

    // ✅ رفض الشركة وحذفها
    #[Route('/reject/{id}', name: 'app_webmaster_reject_company', methods: ['POST'])]
    public function reject(User $company, EntityManagerInterface $em): Response
    {
        $em->remove($company);
        $em->flush();

        $this->addFlash('success', 'تم رفض وحذف حساب الشركة.');
        return $this->redirectToRoute('app_webmaster_dashboard');
    }

    // ✅ إنشاء حساب Admin




    // ✅ إنشاء حساب Admin
    #[Route('/create-admin', name: 'app_webmaster_create_admin', methods: ['POST'])]
    public function createAdmin(
        Request $request,
        EntityManagerInterface $em,
        UserPasswordHasherInterface $passwordHasher
    ): Response {
        $email = $request->request->get('admin_email');
        $password = $request->request->get('admin_password');

        // جلب القيم من الفورم (تأكد أن الأسماء في التويج هي admin_university و admin_department)
        $universityName = $request->request->get('admin_university');
        $departmentName = $request->request->get('admin_department');

        // تحقق أن البريد غير مستخدم
        $existing = $em->getRepository(User::class)->findOneBy(['email' => $email]);
        if ($existing) {
            $this->addFlash('error', 'هذا البريد الإلكتروني مستخدم بالفعل.');
            return $this->redirectToRoute('app_webmaster_dashboard');
        }

        $admin = new User();
        $admin->setEmail($email);
        $admin->setRoles(['ROLE_ADMIN']);
        $admin->setPassword($passwordHasher->hashPassword($admin, $password));

        // إعدادات الحالة
        $admin->setIsVerified(true);
        $admin->setIsApprovedByWebmaster(true);

        // --- إضافة منطق الجامعة والقسم هنا ---

        if ($universityName && $departmentName) {
            // 1. البحث عن الجامعة أو إنشاؤها
            $university = $em->getRepository(University::class)->findOneBy(['name' => $universityName]);
            if (!$university) {
                $university = new University();
                $university->setName($universityName);
                $em->persist($university);
            }

            // 2. البحث عن القسم داخل هذه الجامعة
            $deptObj = $em->getRepository(Department::class)->findOneBy([
                'name' => $departmentName,
                'university' => $university
            ]);

            if (!$deptObj) {
                $deptObj = new Department();
                $deptObj->setName($departmentName);
                $deptObj->setUniversity($university);
                $em->persist($deptObj);
            }

            // 3. ربط الأدمن بالعلاقات الجديدة (الـ Entities)
            $admin->setUniversityRef($university);
            $admin->setDepartmentRef($deptObj);

            // اختياري: إذا أردت الاحتفاظ بالقيم النصية القديمة أيضاً
            $admin->setUniversityName($universityName);
            $admin->setDepartment($departmentName);
        }

        $em->persist($admin);
        $em->flush();

        $this->addFlash('success', 'تم إنشاء حساب الأدمين بنجاح.');
        return $this->redirectToRoute('app_webmaster_dashboard');
    }







    // ✅ حذف أدمن
    #[Route('/delete-admin/{id}', name: 'app_webmaster_delete_admin', methods: ['POST'])]
    public function deleteAdmin(int $id, EntityManagerInterface $em): Response
    {
        $admin = $em->getRepository(User::class)->find($id);
        if ($admin) {
            $em->remove($admin);
            $em->flush();
            $this->addFlash('success', 'تم حذف الأدمن بنجاح.');
        }
        return $this->redirectToRoute('app_webmaster_dashboard');
    }

// ✅ تعديل معلومات أدمن (عرض الصفحة وحفظ البيانات)
    #[Route('/edit-admin/{id}', name: 'app_webmaster_edit_admin')]
    public function editAdmin(int $id, Request $request, EntityManagerInterface $em): Response
    {
        $admin = $em->getRepository(User::class)->find($id);


        if ($request->isMethod('POST')) {
            $admin->setEmail($request->request->get('email'));
            // يمكنك إضافة تعديل الجامعة والقسم هنا بنفس منطق الإنشاء

            $em->flush();
            $this->addFlash('success', 'تم تحديث البيانات بنجاح.');
            return $this->redirectToRoute('app_webmaster_dashboard');
        }

        return $this->render('webmaster/edit_admin.html.twig', [
            'admin' => $admin
        ]);
    }
}