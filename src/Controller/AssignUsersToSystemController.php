<?php

namespace App\Controller;
use App\Entity\Systems;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
class AssignUsersToSystemController extends AbstractController
{
    private $entityManager;
    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }
    #[Route('/assign/users/system{id}', name: 'app_assign_users_to_system')]
    public function index($id): Response
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

       return $this->render('assign_users_to_system/index.html.twig', [
            'users' => $finalUsers,
            'systemId' => $id,
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
