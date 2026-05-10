<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\Request;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Component\HttpFoundation\JsonResponse; // تأكد من إضافة هذا التوصيف في الأعلى
// 1. تأكد من وجود هذه التعريفات في أعلى الملف


final class HomeController extends AbstractController
{
    #[Route('/', name: 'app_home')]
    public function index(EntityManagerInterface $em): Response
    {
       // return $this->render('home/index.html.twig', [
         //   'controller_name' => 'HomeController',
       // ]);

// 1. جلب قائمة الجامعات الفريدة
        $universities = $em->getRepository(User::class)->createQueryBuilder('u')
            ->select('DISTINCT u.universityName')
            ->where('u.universityName IS NOT NULL')
            ->getQuery()
            ->getResult();

        // 2. جلب قائمة كل الأقسام المتاحة حالياً (كخيار افتراضي)
        $results = $em->getRepository(User::class)->createQueryBuilder('u')
            ->select('DISTINCT u.department')
            ->where('u.department IS NOT NULL')
            ->getQuery()
            ->getResult();

        // تنسيق الأقسام لتناسب Twig
        $departments = [];
        foreach ($results as $row) {
            $departments[] = ['department' => $row['department']];
        }
        // في HomeController.php المعدل
        return $this->render('home/index.html.twig', [
            'last_username' => '',
            'error' => null,
            'open_login_modal' => false,
            'departments' => $departments,
            'universities' => $universities,
        ]);
    }


        #[Route('/register', name: 'app_register', methods: ['POST'])]
    public function register(
            Request $request,
            MailerInterface $mailer,
            UserPasswordHasherInterface $passwordHasher,
            EntityManagerInterface $em,
            SluggerInterface $slugger
        ): Response {
            $email = $request->request->get('email');
            $role = $request->request->get('role');
            $password = $request->request->get('password');
            $confirmPassword = $request->request->get('confirm_password');
            $studentDept = $request->request->get('department'); // القسم المختار

            if ($password !== $confirmPassword) {
                $this->addFlash('error', 'كلمات المرور غير متطابقة!');
                return $this->redirectToRoute('app_home');
            }

            $user = new User();
            $user->setEmail($email);
            $user->setRoles([$role]);
            $user->setPassword($passwordHasher->hashPassword($user, $password));
            $user->setIsVerified(false);

            if ($role === 'ROLE_STUDENT') {
                $user->setFirstName($request->request->get('first_name')); // تم التعديل من firstName إلى first_name
                $user->setLastName($request->request->get('last_name'));   // تم التعديل من lastName إلى last_name
                $user->setEmail($request->request->get('email'));
                $user->setDepartment($studentDept);
// لتجنب توقف الموقع بسبب البريد الإلكتروني (مؤقتاً للtest):
                try {
                    $mailer->send($email);
                } catch (\Exception $e) {
                    // تجاهل خطأ الإرسال حالياً لتسمح للحساب بأن يُنشأ في قاعدة البيانات
                }
                // استخراج الدومين (مثلاً: univ-constantine.dz)
                $domain = substr(strrchr($email, "@"), 1);

                // البحث عن الآدمن الذي يطابق نفس الدومين ونفس القسم
                $deptAdmin = $em->getRepository(User::class)->createQueryBuilder('u')
                    ->where('u.roles LIKE :role')
                    ->andWhere('u.email LIKE :domain')
                    ->andWhere('u.department = :dept')
                    ->setParameter('role', '%"ROLE_ADMIN"%')
                    ->setParameter('domain', '%@' . $domain)
                    ->setParameter('dept', $studentDept)
                    ->setMaxResults(1)
                    ->getQuery()
                    ->getOneOrNullResult();

                if (!$deptAdmin) {
                    $this->addFlash('error', 'عذراً، لا يوجد مسؤول مسجل لهذا القسم في جامعتك.');
                    return $this->redirectToRoute('app_home');
                }

                // ربط الطالب بآدمن القسم الخاص به
                $user->setUniversityEntity($deptAdmin);

                // منطق التوكن والإيميل
                $token = bin2hex(random_bytes(32));
                $user->setConfirmationToken($token);

                $url = $this->generateUrl('app_verify_email', ['token' => $token], UrlGeneratorInterface::ABSOLUTE_URL);

                $emailContent = (new Email())
                    ->from('no-reply@stage.io')
                    ->to($email)
                    ->subject('تفعيل حساب الطالب - Stage.io')
                    ->html("مرحباً، يرجى تفعيل حسابك في قسم {$studentDept} بالضغط هنا: <a href='{$url}'>تفعيل</a>");

                $mailer->send($emailContent);
            }

        // Company Logic
        elseif ($role === 'ROLE_COMPANY') {
            $user->setCompanyName($request->request->get('companyName'));
            $user->setWilaya($request->request->get('wilaya'));
            $user->setIsApprovedByWebmaster(false);

            $file = $request->files->get('verification_file');
            if ($file) {
                $originalFilename = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
                $safeFilename = $slugger->slug($originalFilename);
                $newFilename = $safeFilename.'-'.uniqid().'.'.$file->guessExtension();

                try {
                    $file->move(
                        $this->getParameter('kernel.project_dir') . '/public/uploads/verification/',
                        $newFilename
                    );
                    $user->setVerificationFile($newFilename);
                } catch (FileException $e) {
                    $this->addFlash('error', 'An error occurred while uploading the file.');
                    return $this->redirectToRoute('app_home');
                }
            } else {
                $this->addFlash('error', 'Please upload the company verification document.');
                return $this->redirectToRoute('app_home');
            }
        }

        $em->persist($user);
        $em->flush();

        // Success Messages
        if ($role === 'ROLE_COMPANY') {
            $this->addFlash('success', 'Registration successful! Your account is pending review by the Web Master.');
        } else {
            $this->addFlash('success', 'Account created successfully! You can now log in.');
        }

        return $this->redirectToRoute('app_home', ['openLogin' => 1]);
    }




    #[Route('/verify/{token}', name: 'app_verify_email')]
    public function verifyEmail(string $token, EntityManagerInterface $em): Response
    {
        $user = $em->getRepository(User::class)->findOneBy(['confirmationToken' => $token]);

        if (!$user) {
            $this->addFlash('error', 'رابط التفعيل غير صالح.');
            return $this->redirectToRoute('app_home');
        }

        $user->setIsVerified(true);
        $user->setConfirmationToken(null);
        $em->flush();

        $this->addFlash('success', 'تم تفعيل الحساب بنجاح!');
        return $this->redirectToRoute('app_home', ['openLogin' => 1]);
    }






    #[Route('/api/departments/{universityName}', name: 'api_get_departments')]
    public function getDepartmentsByUniversity(string $universityName, EntityManagerInterface $em): JsonResponse
    {
        // نفترض أن الجامعة هي مستخدم بدوره ROLE_ADMIN أو ROLE_WEBMASTER ولديها اسم
        // هنا نجلب الأقسام المرتبطة بمستخدمين يتبعون لهذه الجامعة
        $results = $em->getRepository(User::class)->createQueryBuilder('u')
            ->select('DISTINCT u.department')
            ->where('u.universityName = :uni') // تأكد أن لديك حقل باسم الجامعة في Entity
            ->setParameter('uni', $universityName)
            ->andWhere('u.department IS NOT NULL')
            ->getQuery()
            ->getResult();

        return $this->json($results);
    }

}