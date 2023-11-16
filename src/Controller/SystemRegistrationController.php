<?php

namespace App\Controller;
use App\Form\SystemRegistrationType;
use App\Entity\Device;
use App\Entity\Systems;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\System;
use App\Form\SystemType;
use Symfony\Component\HttpFoundation\Request;
class SystemRegistrationController extends AbstractController
{
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }


    #[Route('/system_registration', name: 'app_system_registration')]
    public function new(Request $request): Response
    {
        $systems = new Systems();
        $form = $this->createForm(SystemRegistrationType::class, $systems);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {

            $this->entityManager->persist($systems);
            $this->entityManager->flush();

            return $this->redirectToRoute('app_home_page_logged'); // Redirect to a success page
        }

        return $this->render('system_registration/index.html.twig', [
            'form' => $form->createView(),
        ]);
    }

}

