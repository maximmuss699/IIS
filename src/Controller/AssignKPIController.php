<?php

namespace App\Controller;
use App\Entity\Device;
use App\Entity\KPI;
use App\Entity\Parameters;
use App\Form\KPIType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Core\Security;

class AssignKPIController extends AbstractController
{
    private $entityManager;
    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    #[Route('/assign/k/p/i/{id}', name: 'app_assign_k_p_i')]
    public function index($id, Request $request,   Security $security, UrlGeneratorInterface $urlGenerator): Response
    {
        $kpi = new KPI();
        $form = $this->createForm(KPIType::class, $kpi);

        $entityManager = $this->entityManager;

        $device = $entityManager->getRepository(Device::class)->find($id);

        $parameters = $device->getType()->getParameters()->toArray();

        $form = $this->createForm(KPIType::class, $kpi, [
            'parameters' => $parameters,
        ]);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Save the submitted KPI entity to the database
            $kpi->setSystems($device->getSystems());
            $entityManager->persist($kpi);
            $entityManager->flush();

            return $this->redirectToRoute('app_system_details', ['id' => $device->getSystems()->getId()]);
        }

        if (!$security->getUser()) {
            $url = $urlGenerator->generate('app_home_page');
            return new RedirectResponse($url);
        }

        $loggedUser = $security->getUser();


        return $this->render('assign_kpi/index.html.twig', [
                'form' => $form->createView(),
                'role' => $loggedUser->getRoles()
            ]);
        }

    }
