<?php

namespace App\Controller;

use App\Entity\Device;
use App\Entity\Systems;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Core\Security;

class AssignDevicesController extends AbstractController
{
    private $entityManager;
    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    #[Route('/assign_devices{id}', name: 'app_assign_devices')]
    public function index($id,  Security $security, UrlGeneratorInterface $urlGenerator): Response
    {

        $deviceRepository = $this->entityManager->getRepository(Device::class);
        $devices = $deviceRepository->findAll();

        $deviceDetails = [];
        foreach ($devices as $device) {
            if (!$device->getSystems() == null)
            {
                if ($id == $device->getSystems()->getId())
                    continue;
            }

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

            // Gather device details in an array
            $deviceDetails[] = [
                'device' => $device,
                'userAlias' => $userAlias,
                'typeName' => $typeName,
                'parameters' => $combinedDetails,
                'imagePath' => $this->getPicture($typeName),
            ];
        }
        if (!$security->getUser()) {
            $url = $urlGenerator->generate('app_home_page');
            return new RedirectResponse($url);
        }

        $loggedUser = $security->getUser();


        return $this->render('assign_devices/index.html.twig', [
            'deviceDetails' => $deviceDetails,
            'systemId' => $id,
            'role' => $loggedUser->getRoles(),
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
    public function getUnit($typeName):string
    {
        return match ($typeName) {
            "Temperature-Sensor" =>  '°C',
            "Pressure-Sensor" => 'bar',
            "Noise-Sensor" => 'db',
            "Humidity-Sensor" => '/img/humidity.png',
            default =>  '/public/img/unknown.png',
        };
    }
    #[Route('/assign_devices', name: 'handle_device_assignment', methods: ['POST'])]
    public function handleDeviceAssignment(Request $request): Response
    {
        if ($request->isMethod('POST')) {
            $formData = $request->request->all();

            // Initialize an empty array for selected device IDs
            $selectedDeviceIds = [];

            // Check if 'selected_devices' key exists in the form data
            if (isset($formData['selected_devices']) && is_array($formData['selected_devices'])) {
                // Loop through each value and add to the selected device IDs array
                foreach ($formData['selected_devices'] as $deviceId) {
                    // Check if the value is a valid integer (or adjust validation as needed)
                    if (is_numeric($deviceId)) {
                        $selectedDeviceIds[] = (int) $deviceId; // Convert to integer and add to the array
                    }
                }
            }
            $systemId = $request->request->get('system_id');
            $system = $this->entityManager->getRepository(Systems::class)->find($systemId);
            $devices = $this->entityManager
                ->getRepository(Device::class)
                ->createQueryBuilder('d')
                ->where('d.id IN (:deviceIds)')
                ->setParameter('deviceIds', $selectedDeviceIds)
                ->getQuery()
                ->getResult();

             foreach ($devices as $device) {
                 $device->setSystems($system); // Assuming there's a method like setSystemId() in your Device entity
                 // Persist changes if needed
                  $this->entityManager->persist($device);
             }
            $this->entityManager->flush();
        }
        // Redirect or render a response if the request method is not POST
        return $this->redirectToRoute('app_system_details', ['id' => $systemId]);
    }

}
