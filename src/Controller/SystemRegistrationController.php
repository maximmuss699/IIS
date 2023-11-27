<?php

namespace App\Controller;
use App\Form\SystemRegistrationType;
use App\Entity\Device;
use App\Entity\Systems;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\System;
use App\Form\SystemType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Core\Security;
class SystemRegistrationController extends AbstractController
{
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }


    #[Route('/system_registration', name: 'app_system_registration')]
    public function new(Request $request, Security $security, UrlGeneratorInterface $urlGenerator): Response
    {
        $systems = new Systems();
        if (!$security->getUser()) {
            $url = $urlGenerator->generate('app_home_page');
            return new RedirectResponse($url);
        }

        $user = $security->getUser();
        if ($user !== null) {
            $form = $this->createForm(SystemRegistrationType::class, $systems);
            $form->handleRequest($request);
            if ($form->isSubmitted() && $form->isValid()) {
                $systems->addUser($user);
                $systems->setUserOwner($user);
                $this->entityManager->persist($systems);
                $this->entityManager->flush();

                return $this->redirectToRoute('app_home_page_logged'); // Redirect to a success page
            }

            return $this->render('system_registration/index.html.twig', [
                'form' => $form->createView(),
                'role' => $user->getRoles()
            ]);
        } else {
            // Handle the case when the user is not authenticated
            // For example, redirect to the login page or display an error message
            return $this->redirectToRoute('app_home_page'); // Replace with your login route
        }
    }


}

