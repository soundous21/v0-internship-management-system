<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted('ROLE_COMPANY')]
class CompanyController extends AbstractController
{
    /**
     * لوحة التحكم الرئيسية للشركة
     */
    public function dashboard(): Response
    {
        // بيانات الشركة من قاعدة البيانات (حالياً hardcoded)
        $companyData = [
            'name' => 'تقنيات الفن',
            'logo' => '/images/company-logo.png'
        ];

        // الإحصائيات
        $stats = [
            'active_offers' => 4,
            'total_applications' => 47,
            'interviews_scheduled' => 8,
            'positions_filled' => 3
        ];

        // أحدث المرشحين
        $recentCandidates = [
            [
                'id' => 1,
                'name' => 'Ahmed Benali',
                'position' => 'Frontend Developer Intern',
                'university' => 'USTHB',
                'skills' => ['React', 'TypeScript', 'Tailwind CSS'],
                'status' => 'new',
                'image' => '/images/student-1.jpg'
            ],
            [
                'id' => 2,
                'name' => 'Fatima Zahra',
                'position' => 'Backend Developer Intern',
                'university' => 'Tlemcen',
                'skills' => ['Laravel', 'MySQL', 'Docker'],
                'status' => 'reviewed',
                'image' => '/images/student-2.jpg'
            ],
            [
                'id' => 3,
                'name' => 'Mohamed Ali',
                'position' => 'UI/UX Designer Intern',
                'university' => 'Constantine',
                'skills' => ['Figma', 'UI Design', 'Prototyping'],
                'status' => 'new',
                'image' => '/images/student-3.jpg'
            ],
            [
                'id' => 4,
                'name' => 'Layla Hassan',
                'position' => 'Full Stack Developer Intern',
                'university' => 'Oran',
                'skills' => ['React', 'Node.js', 'MongoDB'],
                'status' => 'interviewed',
                'image' => '/images/student-4.jpg'
            ]
        ];

        // الفرص النشطة
        $activeOffers = [
            [
                'id' => 1,
                'title' => 'Frontend Developer Intern',
                'applicationsCount' => 18,
                'viewsCount' => 124,
                'status' => 'active',
                'deadline' => '2026-04-30',
                'duration' => '3 months'
            ],
            [
                'id' => 2,
                'title' => 'Backend Developer Intern',
                'applicationsCount' => 12,
                'viewsCount' => 89,
                'status' => 'active',
                'deadline' => '2026-05-15',
                'duration' => '3 months'
            ],
            [
                'id' => 3,
                'title' => 'UI/UX Designer Intern',
                'applicationsCount' => 9,
                'viewsCount' => 65,
                'status' => 'active',
                'deadline' => '2026-05-01',
                'duration' => '2 months'
            ],
            [
                'id' => 4,
                'title' => 'Full Stack Developer Intern',
                'applicationsCount' => 8,
                'viewsCount' => 52,
                'status' => 'paused',
                'deadline' => '2026-06-01',
                'duration' => '3 months'
            ]
        ];

        return $this->render('company/dashboard.html.twig', [
            'company' => $companyData,
            'stats' => $stats,
            'recentCandidates' => $recentCandidates,
            'activeOffers' => $activeOffers
        ]);
    }

    /**
     * عرض قائمة الفرص
     */
    public function offers(): Response
    {
        return $this->render('company/offers.html.twig');
    }

    /**
     * إنشاء فرصة جديدة
     */
    public function createOffer(): Response
    {
        return $this->render('company/create-offer.html.twig');
    }

    /**
     * تحرير فرصة موجودة
     */
    public function editOffer(int $id): Response
    {
        return $this->render('company/edit-offer.html.twig', [
            'offerId' => $id,
        ]);
    }

    /**
     * حذف فرصة
     */
    public function deleteOffer(int $id): Response
    {
        return $this->redirectToRoute('company_offers');
    }

    /**
     * عرض قائمة المرشحين
     */
    public function candidates(): Response
    {
        return $this->render('company/candidates.html.twig');
    }

    /**
     * عرض ملف الشركة
     */
    public function profile(): Response
    {
        return $this->render('company/profile.html.twig');
    }
}

