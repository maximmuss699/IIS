<?php

namespace App\Controller;
use Symfony\Component\HttpKernel\KernelInterface;
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
    public function getUnit($typeName):string
    {
        return match ($typeName) {
            "Temperature-Sensor" =>  '°C',
            "Pressure-Sensor" => 'bar',
            "Noise-Sensor" => 'db',
            default =>  '/public/img/unknown.png',
        };
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
        $systemKPIs = [];
        foreach ($devices as $device) {
            if ($device->getSystems() == null)
                continue;
            if ($id != $device->getSystems()->getId())
                continue;

            $userAlias = $device->getUserAlias();
            $typeName = $device->getType()->getName();
            $parameters = $device->getType()->getParameters()->toArray();
            $units = [];

            foreach ($parameters as $param) {
                $units[] = $this->getUnit($param->getType()->getName());
            }

            usort($parameters, function($a, $b) {
                return $a->getId() - $b->getId();
            });

            $combinedDetails = [];

            foreach ($parameters as $index => $param) {
                $combinedDetails[] = [
                    'parameter' => $param,
                    'unit' => $units[$index],
                ];
            }
            $deviceDetails[] = [
                'device' => $device,
                'userAlias' => $userAlias,
                'typeName' => $typeName,
                'parameters' => $combinedDetails,
                'imagePath' => $this->getPicture($typeName),
            ];
        }

        foreach ($kpis as $kpi) {
            if ($kpi->getSystems()->getId() == $id) {
                $array = $kpi->getParameter()->getValues();
                $kpiF = $kpi->getFunction();
                $systemKPIs[] = [
                    'function' => $this->getFunction($kpiF),
                    'value'=> $kpi->getValue(),
                    'paramName' => $kpi->getParameter()->getName(),
                    'paramVal' => end($array),
                    'result' => $this->callKpi($kpiF, end($array), $kpi->getValue()),
                    'id' => $kpi->getId()
                ];
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

    public function getPicture($typeName):string
    {
        return match ($typeName) {
            "Temperature-Sensor" =>  '/img/temperature.png',
            "Pressure-Sensor" => '/img/pressure.png',
            "Noise-Sensor" => '/img/noise.png',
            default =>  '/public/img/unknown.png',
        };
    }
    public function callKpi($kpiF, $parVal, $kpiVal):string
    {
        switch ($kpiF)
        {
            case "gt":
                if ($kpiVal < $parVal)
                    return "true";
                break;
            case "lt":
                if ($kpiVal > $parVal)
                    return "true";
                break;
            case "eq":
                if ($kpiVal == $parVal)
                    return "true";
                break;
            case "neq":
                if ($kpiVal != $parVal)
                    return "true";
                break;
            default:
                return (string) $kpiF;
        }
        return "false";
    }
    public function getFunction($kip): string
    {
        return match ($kip) {
            "lt" => "less then",
            "gt" => "more then",
            "eq" => "equal to",
            "neq" => "not equal to",
            default => "error function not found",
        };

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

#[Route('/removeKPI/{id}', name: 'remove_kpi')]
    public function removeKPI(Request $request, int $id): Response
{
    $kpiID = $request->request->get('kpi_id');
    $entityManager = $this->entityManager;
    $kpi = $entityManager->getRepository(KPI::class)->find($kpiID);
    $kpi->setParameter(null);
    $kpi->setSystems(null);
    $entityManager->remove($kpi);
    $entityManager->flush();

    // Redirect to the forum page or wherever you want after deletion
    return $this->redirectToRoute('app_system_details', ['id' => $id]);    }

}
