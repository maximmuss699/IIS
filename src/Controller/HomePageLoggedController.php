<?php

namespace App\Controller;
use App\Entity\Device;
use App\Entity\Systems;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Core\Security;

class HomePageLoggedController extends AbstractController
{
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    #[Route('/homepage/Logged', name: 'app_home_page_logged')]
    public function index(Security $security): Response
    {
        $systemRepository = $this->entityManager->getRepository(Systems::class);
        $systems = $systemRepository->findAll();

        $loggedUser = $security->getUser();

        $userSystems = [];
        foreach ($systems as $system)
        {
            foreach ($system->getUsers() as $user)
            {
                if ($user == $loggedUser)
                    $userSystems[] = $system;
            }
        }


        return $this->render('home_page_logged/index.html.twig', [
            'systems' => $userSystems,
        ]);

        return $this->render('home_page_logged/index.html.twig', [
            'controller_name' => 'HomePageLoggedController',
        ]);
    }


}
