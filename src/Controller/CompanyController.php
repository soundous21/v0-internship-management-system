<?php

namespace App\Controller;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\String\Slugger\SluggerInterface;

#[IsGranted('ROLE_COMPANY')]
class CompanyController extends AbstractController
{
    #[Route('/company/dashboard', name: 'app_company_dashboard')]
    public function index(): Response
    {
        $company = $this->getUser();
        return $this->render('company/dashboard.html.twig', [
            'company' => $company,
        ]);
    }

    #[Route('/company/profile/update', name: 'app_company_profile_update', methods: ['POST'])]
    public function updateProfile(Request $request, EntityManagerInterface $entityManager, SluggerInterface $slugger): JsonResponse
    {
        /** @var User $company */
        $company = $this->getUser();

        try {
            // 1. استقبال البيانات النصية (لأننا أرسلناها كـ FormData)
            $company->setCompanyName($request->request->get('companyName'));
            $company->setEmail($request->request->get('email'));
            $company->setPhone($request->request->get('phone'));
            $company->setIndustry($request->request->get('industry'));
            $company->setWilaya($request->request->get('wilaya'));
            $company->setWebsite($request->request->get('website'));
            $company->setBio($request->request->get('bio'));

            // 2. معالجة رفع الشعار (Logo) إذا وجد
            $logoFile = $request->files->get('logo');
            if ($logoFile) {
                $originalFilename = pathinfo($logoFile->getClientOriginalName(), PATHINFO_FILENAME);
                $safeFilename = $slugger->slug($originalFilename);
                $newFilename = $safeFilename . '-' . uniqid() . '.' . $logoFile->guessExtension();

                $logoFile->move(
                    $this->getParameter('logos_directory'), // تأكدي من تعريف هذا المسار في services.yaml
                    $newFilename
                );
                // تأكدي أن لديكِ حقل setLogo في Entity User
                if (method_exists($company, 'setLogo')) {
                    $company->setLogo($newFilename);
                }
            }

            $entityManager->flush();

            return new JsonResponse(['status' => 'success', 'message' => 'Saved successfully!']);

        } catch (\Exception $e) {
            return new JsonResponse(['status' => 'error', 'message' => 'Error: ' . $e->getMessage()], 500);
        }
    }

    // src/Controller/CompanyController.php

    #[Route('/company/profile', name: 'app_company_profile')]
    public function profile(): Response
    {
        $company = $this->getUser();
        // هنا نقوم بعرض نفس القالب الذي يحتوي على الفورم
        return $this->render('company/dashboard.html.twig', [
            'company' => $company,
        ]);
    }
}