<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Component\HttpFoundation\Request;
use App\Entity\User;                          // ← أضيفي
use Doctrine\ORM\EntityManagerInterface;      // ← أضيفي




class SecurityController extends AbstractController
{
    #[Route('/login', name: 'app_login')]
    public function login(
        AuthenticationUtils $authUtils,
        EntityManagerInterface $em            // ← أضيفي
    ): Response {
        if ($this->getUser()) {
            return $this->redirectToRoute('app_dashboard_redirect');
        }

        $error = $authUtils->getLastAuthenticationError();
        $lastUsername = $authUtils->getLastUsername();

        // ← أضيفي هذا الكود
        $universities = $em->getRepository(User::class)->createQueryBuilder('u')
            ->select('DISTINCT u.universityName')
            ->where('u.universityName IS NOT NULL')
            ->getQuery()
            ->getResult();

        $results = $em->getRepository(User::class)->createQueryBuilder('u')
            ->select('DISTINCT u.department')
            ->where('u.department IS NOT NULL')
            ->getQuery()
            ->getResult();

        $departments = [];
        foreach ($results as $row) {
            $departments[] = ['department' => $row['department']];
        }

        return $this->render('home/index.html.twig', [
            'last_username' => $lastUsername,
            'error' => $error,
            'open_login_modal' => $error !== null,
            'universities' => $universities,   // ← أضيفي
            'departments' => $departments,     // ← أضيفي
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
        if ($this->isGranted('ROLE_WEBMASTER')) {
            return $this->redirectToRoute('app_webmaster_dashboard');
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