<?php

namespace App\Controller;


// src/Controller/WebmasterController.php

namespace App\Controller;

    use App\Entity\User;
    use Doctrine\ORM\EntityManagerInterface;
    use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
    use Symfony\Component\HttpFoundation\Response;
    use Symfony\Component\Routing\Annotation\Route;
    use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted('ROLE_WEBMASTER')]
#[Route('/webmaster')]
class WebmasterController extends AbstractController
{
    #[Route('/dashboard', name: 'app_webmaster_dashboard')]
    public function index(EntityManagerInterface $em): Response
    {
        // جلب الشركات التي لم يتم الموافقة عليها بعد
        $pendingCompanies = $em->getRepository(User::class)->createQueryBuilder('u')
            ->where('u.roles LIKE :role')
            ->andWhere('u.isApprovedByWebmaster = :status')
            ->setParameter('role', '%"ROLE_COMPANY"%')
            ->setParameter('status', false)
            ->getQuery()
            ->getResult();

        return $this->render('webmaster/dashboard.html.twig', [
            'companies' => $pendingCompanies,
        ]);
    }

    #[Route('/approve/{id}', name: 'app_webmaster_approve_company', methods: ['POST'])]
    public function approve(User $company, EntityManagerInterface $em): Response
    {
        $company->setIsApprovedByWebmaster(true);
        $em->flush();

        $this->addFlash('success', 'تم تفعيل حساب الشركة بنجاح.');
        return $this->redirectToRoute('app_webmaster_dashboard');
    }

}