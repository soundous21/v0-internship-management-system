<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
{
    /**
     * عرض الصفحة الرئيسية
     */
    public function index(): Response
    {
        $features = [
            [
                'icon' => '🎯',
                'title' => 'For Students',
                'description' => 'Find the best internship opportunities that match your skills and career goals.',
            ],
            [
                'icon' => '🏢',
                'title' => 'For Companies',
                'description' => 'Connect with talented students and build your future workforce.',
            ],
            [
                'icon' => '📊',
                'title' => 'For Universities',
                'description' => 'Manage internships, track student progress, and validate agreements.',
            ],
        ];

        return $this->render('home/index.html.twig', [
            'features' => $features,
        ]);
    }

    /**
     * عرض صفحة حول التطبيق
     */
    public function about(): Response
    {
        return $this->render('home/about.html.twig');
    }

    /**
     * عرض صفحة التواصل
     */
    public function contact(): Response
    {
        return $this->render('home/contact.html.twig');
    }
}
