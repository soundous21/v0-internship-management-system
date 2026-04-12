<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class HomeController extends AbstractController
{
    #[Route('/', name: 'app_home')]
    public function index(): Response
    {
       // return $this->render('home/index.html.twig', [
         //   'controller_name' => 'HomeController',
       // ]);
        // في HomeController.php المعدل
        return $this->render('home/index.html.twig', [
            'last_username' => '',
            'error' => null,
            'open_login_modal' => false,
        ]);
    }


    // أضيفي هذه الدالة مؤقتاً هنا
    #[Route('/register', name: 'app_register', methods: ['POST'])]
    public function register(): Response
    {
        // هنا سيتم وضع كود البرمجة لاحقاً (Entities, PasswordHasher, etc.)
        return new Response('Registration Logic will be here');
    }
}