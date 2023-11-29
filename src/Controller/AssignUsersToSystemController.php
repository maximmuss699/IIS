<?php

namespace App\Controller;
use App\Entity\Systems;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Core\Security;

class AssignUsersToSystemController extends AbstractController
{
    private $entityManager;
    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }
    #[Route('/assign/users/system{id}', name: 'app_assign_users_to_system')]
    public function index($id,  Security $security, UrlGeneratorInterface $urlGenerator): Response
    {
        $userRepository = $this->entityManager->getRepository(User::class);
        $users = $userRepository->findAll();
        $finalUsers = [];
        foreach ($users as $user)
        {
            $userSystems = $user->getSystems();
            foreach ($userSystems as $system)
            {
                if ($system->getId() == $id)
                {
                    $finalUsers[] = $user;
                }

            }
        }
        if (!$security->getUser()) {
            $url = $urlGenerator->generate('app_home_page');
            return new RedirectResponse($url);
        }

        $loggedUser = $security->getUser();


        return $this->render('assign_users_to_system/index.html.twig', [
            'users' => $finalUsers,
            'systemId' => $id,
            'role' => $loggedUser->getRoles()
        ]);
    }
    #[Route('/assign/users/system', name: 'handle_user_assignment', methods: ['POST'])]
    public function handleUserAssignment (Request $request): Response
    {
        $email = $request->request->get('email');
        $systemId = $request->request->get('systemId'); // Make sure the key matches the AJAX payload

        $userRepository = $this->entityManager->getRepository(User::class);
        $systemRepository = $this->entityManager->getRepository(Systems::class);

        $system = $systemRepository->find($systemId);
        $user = $userRepository->findOneBy(['email' => $email]);

        if ($user) {
            if ($system && !$user->getSystems()->contains($system)) {
                $system->addUser($user);
                $this->entityManager->persist($system);
                $this->entityManager->flush();
                return new Response('success');
            } else {
                return new Response('User already added.' );
            }
        }
        else {
            return new Response('User does not exist.', 404);
        }
    }

}
