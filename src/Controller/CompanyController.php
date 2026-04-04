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
        return $this->render('company/dashboard.html.twig');
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
        // منطق الحذف
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
