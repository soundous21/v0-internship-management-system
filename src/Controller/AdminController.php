<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted('ROLE_ADMIN')]
class AdminController extends AbstractController
{
    /**
     * لوحة التحكم الرئيسية للمسؤول
     */
    public function dashboard(): Response
    {
        return $this->render('admin/dashboard.html.twig');
    }

    /**
     * عرض قائمة التحققات المعلقة
     */
    public function validations(): Response
    {
        return $this->render('admin/validations.html.twig');
    }

    /**
     * الموافقة على تحقق
     */
    public function approveValidation(int $id): Response
    {
        // منطق الموافقة
        return $this->redirectToRoute('admin_validations');
    }

    /**
     * رفض تحقق
     */
    public function rejectValidation(int $id): Response
    {
        // منطق الرفض
        return $this->redirectToRoute('admin_validations');
    }

    /**
     * عرض قائمة الاتفاقيات
     */
    public function agreements(): Response
    {
        return $this->render('admin/agreements.html.twig');
    }

    /**
     * توقيع اتفاقية
     */
    public function signAgreement(int $id): Response
    {
        // منطق التوقيع
        return $this->redirectToRoute('admin_agreements');
    }

    /**
     * عرض الإحصائيات
     */
    public function statistics(): Response
    {
        return $this->render('admin/statistics.html.twig');
    }
}
