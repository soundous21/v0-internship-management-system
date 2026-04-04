<?php

namespace App\Controller;

use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Doctrine\ORM\EntityManagerInterface;

class AuthController extends AbstractController
{
    /**
     * تسجيل مستخدم جديد
     */
    public function register(Request $request, EntityManagerInterface $em, UserPasswordHasherInterface $passwordHasher): Response
    {
        if ($request->isMethod('POST')) {
            $email = $request->request->get('email');
            $password = $request->request->get('password');
            $role = $request->request->get('role');

            // التحقق من وجود المستخدم
            $existingUser = $em->getRepository(User::class)->findOneBy(['email' => $email]);
            if ($existingUser) {
                $this->addFlash('error', 'هذا البريد الإلكتروني موجود بالفعل');
                return $this->redirectToRoute('app_register');
            }

            // إنشاء مستخدم جديد
            $user = new User();
            $user->setEmail($email);
            $user->setPassword($passwordHasher->hashPassword($user, $password));
            $user->setRoles(['ROLE_' . strtoupper($role)]);
            $user->setCreatedAt(new \DateTimeImmutable());

            $em->persist($user);
            $em->flush();

            $this->addFlash('success', 'تم التسجيل بنجاح! الرجاء تسجيل الدخول.');
            return $this->redirectToRoute('app_login');
        }

        return $this->render('auth/register.html.twig');
    }

    /**
     * تسجيل الدخول
     */
    public function login(AuthenticationUtils $authenticationUtils): Response
    {
        if ($this->getUser()) {
            // إذا كان المستخدم مسجل دخول بالفعل
            $roles = $this->getUser()->getRoles();
            if (in_array('ROLE_STUDENT', $roles)) {
                return $this->redirectToRoute('student_dashboard');
            } elseif (in_array('ROLE_COMPANY', $roles)) {
                return $this->redirectToRoute('company_dashboard');
            } elseif (in_array('ROLE_ADMIN', $roles)) {
                return $this->redirectToRoute('admin_dashboard');
            }
        }

        $error = $authenticationUtils->getLastAuthenticationError();
        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render('auth/login.html.twig', [
            'last_username' => $lastUsername,
            'error' => $error,
        ]);
    }

    /**
     * تسجيل الخروج
     */
    public function logout(): Response
    {
        // تعالج بواسطة Symfony security
        throw new \LogicException('This method can be blank - it will be intercepted by the logout key on your firewall.');
    }
}
