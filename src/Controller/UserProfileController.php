<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\UserProfileType;
use Doctrine\ORM\EntityManagerInterface;
use phpDocumentor\Reflection\Types\Null_;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Core\User\InMemoryUser;
use Symfony\Component\HttpFoundation\Request;

use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
class UserProfileController extends AbstractController
{

    private $entityManager;
    public function __construct(EntityManagerInterface $entityManager, private UrlGeneratorInterface $urlGenerator)
    {
        $this->entityManager = $entityManager;
    }

    #[Route('/user/profile', name: 'app_user_profile')]
    public function index(Request $request): Response
    {
        // Get the current user
        $user = $this->getUser();

        if (!$user instanceof User) {
            throw $this->createNotFoundException('User not found');
        }

        $form = $this->createForm(UserProfileType::class, $user);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->entityManager;
            $entityManager->persist($user);
            $entityManager->flush();

            $this->addFlash('success', 'Profile updated successfully!');

            return $this->redirectToRoute('app_home_page_logged');
        }

        if (in_array('ROLE_ADMIN', $user->getRoles()))
        {
            return new RedirectResponse($this->urlGenerator->generate('app_admin'));
        } else {
            return $this->render('user/profile.html.twig', [
                'form' => $form->createView(),
            ]);
        }
    }
}
