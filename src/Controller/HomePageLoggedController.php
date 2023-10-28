<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HomePageLoggedController extends AbstractController
{
    #[Route('/homepage/Logged', name: 'app_home_page_logged')]
    public function index(): Response
    {
        return $this->render('home_page_logged/index.html.twig', [
            'controller_name' => 'HomePageLoggedController',
        ]);
    }
}
