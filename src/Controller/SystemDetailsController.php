<?php

namespace App\Controller;

use App\Entity\Device;
use App\Entity\KPI;
use App\Entity\Systems;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\ORM\EntityManagerInterface;
class SystemDetailsController extends AbstractController
{
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    #[Route('/system_details{id}', name: 'app_system_details')]
    public function index($id): Response
    {
        $deviceRepository = $this->entityManager->getRepository(Device::class);
        $devices = $deviceRepository->findAll();
        $systemR = $this->entityManager->getRepository(Systems::class);
        $system = $systemR->find($id);
        $user = $this->getUser();
        $kpiR = $this->entityManager->getRepository(KPI::class);
        $kpis = $kpiR->findAll();
        $deviceDetails = [];
        foreach ($devices as $device) {
            if ($device->getSystems() == null)
                continue;
            if ($id != $device->getSystems()->getId())
                continue;

            $userAlias = $device->getUserAlias();
            $typeName = $device->getType()->getName();
            $parameters = $device->getType()->getParameters();

            // Gather device details in an array
            $deviceDetails[] = [
                'device' => $device,
                'userAlias' => $userAlias,
                'typeName' => $typeName,
                'parameters' => $parameters,
            ];
        }
        $systemKPIs = [];

        foreach ($kpis as $kpi) {
            if ($kpi->getSystems()->getId() == $id) {
                $systemKPIs[] = $kpi; // Collecting all KPIs related to the specified system ID
            }
        }

        return $this->render('system_details/index.html.twig', [
            'deviceDetails' => $deviceDetails,
            'systemId' => $id,
            'systemOwner' => $system->getUserOwner(),
            'user' => $user,
            'kpis' => $systemKPIs,
        ]);
    }
    #[Route('/disconnect/{id}', name: 'disconnect_device')]
    public function disconnectDevice(Request $request, int $id): Response
    {
        $deviceId = $request->request->get('device_id');
        $entityManager = $this->entityManager;
        $system = $entityManager->getRepository(Systems::class)->find($id);
        $device = $entityManager->getRepository(Device::class)->find($deviceId);

        if (!$system) {
            throw $this->createNotFoundException('System not found');
        }
        if ($device->getSystems() == $system)
        {
            $device->setSystems(null);
        }
        $entityManager->flush();

        // Redirect to the forum page or wherever you want after deletion
        return $this->redirectToRoute('app_system_details', ['id' => $id]);    }

}
