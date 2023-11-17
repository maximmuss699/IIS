<?php

namespace App\Controller;
use App\Entity\Device;
use App\Entity\Systems;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
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
    }

    #[Route('/delete-system/{id}', name: 'delete_system')]
    public function deleteSystem(Request $request, int $id): Response
    {
        $entityManager = $this->entityManager;
        $system = $entityManager->getRepository(Systems::class)->find($id);
        $devices = $entityManager->getRepository(Device::class)->findAll();

        if (!$system) {
            throw $this->createNotFoundException('System not found');
        }
        foreach ($devices as $device)
        {
            if ($device->getSystems() == $system)
            {
                $device->setSystems(null);
            }
        }
        $entityManager->flush();
        $entityManager->remove($system);

        $entityManager->flush();
        // Redirect to the forum page or wherever you want after deletion
        return $this->redirectToRoute('app_home_page_logged');
    }
}
