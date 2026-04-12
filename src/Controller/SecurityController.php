<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Component\HttpFoundation\Request;



class SecurityController extends AbstractController
{
    #[Route('/login', name: 'app_login')]
    public function login(AuthenticationUtils $authUtils): Response
    {
        // إذا كان المستخدم مسجل دخوله فعلاً، وجهه للداشبورد مباشرة
        if ($this->getUser()) {
            return $this->redirectToRoute('app_dashboard_redirect');
        }

        // جلب أخطاء تسجيل الدخول إن وجدت
        $error = $authUtils->getLastAuthenticationError();
        $lastUsername = $authUtils->getLastUsername();

        // التوجيه لصفحة الـ Home مع إرسال الأخطاء
        return $this->render('home/index.html.twig', [
            'last_username' => $lastUsername,
            'error' => $error,
            'open_login_modal' => $error !== null,
        ]);
    }

    #[Route('/logout', name: 'app_logout')]
    public function logout(): void
    {
    }

    #[Route('/dashboard', name: 'app_dashboard_redirect')]
    public function dashboardRedirect(): Response
    {
        if ($this->isGranted('ROLE_ADMIN')) {
            return $this->redirectToRoute('app_admin_dashboard');
        }
        if ($this->isGranted('ROLE_COMPANY')) {
            return $this->redirectToRoute('app_company_dashboard');
        }
        if ($this->isGranted('ROLE_STUDENT')) {
            return $this->redirectToRoute('app_student_dashboard');
        }

        return $this->redirectToRoute('app_home');
    }









    // src/Controller/SecurityController.php

    // src/Controller/SecurityController.php


    #[Route('/forgot-password', name: 'app_forgot_password')]
    public function forgotPassword(Request $request): Response
    {
        if ($request->isMethod('POST')) {
            $email = $request->request->get('email');


            $this->addFlash('success', 'If an account exists for ' . $email . ', a reset link has been sent.');
            return $this->redirectToRoute('app_home');
        }

        return $this->render('security/forgot_password.html.twig');
    }}