<?php

namespace App\Controller;
use App\Entity\Device;
use App\Entity\KPI;
use App\Entity\Parameters;
use App\Form\KPIType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
class AssignKPIController extends AbstractController
{
    private $entityManager;
    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    #[Route('/assign/k/p/i/{id}', name: 'app_assign_k_p_i')]
    public function index($id, Request $request): Response
    {
        $kpi = new KPI();
        $form = $this->createForm(KPIType::class, $kpi);

        $entityManager = $this->entityManager;

        // Fetch the device based on the provided ID
        $device = $entityManager->getRepository(Device::class)->find($id);

        // Fetch parameters filtered by the device's type
        $parameters = $device->getType()->getParameters()->toArray();

        // Pass filtered parameters to the form builder options
        $form = $this->createForm(KPIType::class, $kpi, [
            'parameters' => $parameters,
        ]);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Save the submitted KPI entity to the database
            $kpi->setSystems($device->getSystems());
            $entityManager->persist($kpi);
            $entityManager->flush();

            // Redirect or do something else upon successful form submission
        }


        return $this->render('assign_kpi/index.html.twig', [
                'form' => $form->createView(),
            ]);
        }

    }
