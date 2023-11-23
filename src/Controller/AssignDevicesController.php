<?php

namespace App\Controller;

use App\Entity\Device;
use App\Entity\Systems;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
class AssignDevicesController extends AbstractController
{
    private $entityManager;
    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    #[Route('/assign_devices{id}', name: 'app_assign_devices')]
    public function index($id): Response
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
            $parameters = $device->getType()->getParameters();

            // Gather device details in an array
            $deviceDetails[] = [
                'device' => $device,
                'userAlias' => $userAlias,
                'typeName' => $typeName,
                'parameters' => $parameters,
            ];
        }
        return $this->render('assign_devices/index.html.twig', [
            'deviceDetails' => $deviceDetails,
            'systemId' => $id,
            ]);
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
