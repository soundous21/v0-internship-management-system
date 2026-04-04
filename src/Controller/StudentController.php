<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted('ROLE_STUDENT')]
class StudentController extends AbstractController
{
    /**
     * لوحة التحكم الرئيسية للطالب
     */
    public function dashboard(): Response
    {
        return $this->render('student/dashboard.html.twig');
    }

    /**
     * عرض قائمة الفرص
     */
    public function offers(): Response
    {
        return $this->render('student/offers.html.twig');
    }

    /**
     * عرض تفاصيل فرصة محددة
     */
    public function offerDetail(int $id): Response
    {
        return $this->render('student/offer-detail.html.twig', [
            'offerId' => $id,
        ]);
    }

    /**
     * تقديم طلب للفرصة
     */
    public function apply(int $id): Response
    {
        return $this->render('student/apply.html.twig', [
            'offerId' => $id,
        ]);
    }

    /**
     * عرض ملف الطالب الشخصي
     */
    public function profile(): Response
    {
        return $this->render('student/profile.html.twig');
    }

    /**
     * عرض قائمة التطبيقات
     */
    public function applications(): Response
    {
        return $this->render('student/applications.html.twig');
    }
}
