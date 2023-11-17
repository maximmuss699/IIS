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

       return $this->render('assign_users_to_system/index.html.twig', [
            'users' => $users,
            'systemId' => $id,
        ]);
    }
    #[Route('/assign/users/system', name: 'handle_user_assignment', methods: ['POST'])]
    public function handleUserAssignment (Request $request): Response
    {
        if ($request->isMethod('POST')) {
            $formData = $request->request->all();

            // Initialize an empty array for selected device IDs
            $selectedUserIds = [];

            // Check if 'selected_devices' key exists in the form data
            if (isset($formData['selected_users']) && is_array($formData['selected_users'])) {
                foreach ($formData['selected_users'] as $userId) {
                    // Check if the value is a valid integer (or adjust validation as needed)
                    if (is_numeric($userId)) {
                        $selectedUserIds[] = (int) $userId; // Convert to integer and add to the array
                    }
                }
            }

            $systemId = $request->request->get('system_id');
            $system = $this->entityManager->getRepository(Systems::class)->find($systemId);
            $users = $this->entityManager
                ->getRepository(User::class)
                ->createQueryBuilder('u')
                ->where('u.id IN (:selectedIds)')
                ->setParameter('selectedIds', $selectedUserIds)
                ->getQuery()
                ->getResult();
            foreach ($users as $user) {
                $system->addUser($user);
                $this->entityManager->persist($system);
                $this->entityManager->flush();
            }
        }
        // Redirect or render a response if the request method is not POST
        return $this->redirectToRoute('app_home_page_logged');
    }

}
