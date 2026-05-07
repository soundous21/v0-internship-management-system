<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\Request;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;


final class HomeController extends AbstractController
{
    #[Route('/', name: 'app_home')]
    public function index(): Response
    {
       // return $this->render('home/index.html.twig', [
         //   'controller_name' => 'HomeController',
       // ]);
        // في HomeController.php المعدل
        return $this->render('home/index.html.twig', [
            'last_username' => '',
            'error' => null,
            'open_login_modal' => false,
        ]);
    }

    #[Route('/register', name: 'app_register', methods: ['POST'])]
    public function register(
        Request $request,
        UserPasswordHasherInterface $passwordHasher,
        EntityManagerInterface $em
    ): Response {
        $email = $request->request->get('email');
        $role = $request->request->get('role');
        $password = $request->request->get('password');

        // إنشاء كائن مستخدم جديد
        $user = new User();
        $user->setEmail($email);
        $user->setRoles([$role]);
        $user->setPassword($passwordHasher->hashPassword($user, $password));

        // منطق خاص بالطلاب: التحقق من الدومين والربط بالجامعة
        if ($role === 'ROLE_STUDENT') {
            $user->setFirstName($request->request->get('firstName'));
            $user->setLastName($request->request->get('lastName'));

            // 1. استخراج الدومين من الإيميل (مثال: univ-constantine.dz)
            $domain = substr(strrchr($email, "@"), 1);

            // 2. البحث عن أدمن (جامعة) يطابق إيميله هذا الدومين
            $universityAdmin = $em->getRepository(User::class)->createQueryBuilder('u')
                ->where('u.roles LIKE :role')
                ->andWhere('u.email LIKE :domain')
                ->setParameter('role', '%"ROLE_ADMIN"%')
                ->setParameter('domain', '%@' . $domain)
                ->setMaxResults(1)
                ->getQuery()
                ->getOneOrNullResult();

            // 3. التحقق: إذا لم يوجد أدمن لهذا الدومين، نرفض التسجيل
            if (!$universityAdmin) {
                $this->addFlash('error', 'عذراً، جامعتك غير مسجلة في نظامنا حالياً.');
                return $this->redirectToRoute('app_home');
            }

            // 4. الربط التلقائي
            $user->setUniversityEntity($universityAdmin); // ربط العلاقة البرمجية
            $user->setUniversity($universityAdmin->getUniversityName() ?? $universityAdmin->getCompanyName()); // تعبئة حقل الجامعة نصياً
        }

        // منطق تسجيل الشركة (اختياري)
        elseif ($role === 'ROLE_COMPANY') {
            $user->setCompanyName($request->request->get('companyName'));
            $user->setWilaya($request->request->get('wilaya'));
        }

        // حفظ البيانات
        $em->persist($user);
        $em->flush();

        $this->addFlash('success', 'تم إنشاء الحساب بنجاح! يمكنك تسجيل الدخول الآن.');
        return $this->redirectToRoute('app_home', ['openLogin' => 1]);
    }
}