<?php

namespace App\Controller;

use App\Entity\Parameters;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Device;
use Symfony\Component\HttpFoundation\Request;
class BrookerController extends AbstractController
{

    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }


    #[Route('/brooker', name: 'app_brooker')]
    public function index(): Response
    {
        $deviceRepository = $this->entityManager->getRepository(Device::class);
        $devices = $deviceRepository->findAll();
        $deviceDetails = [];
        foreach ($devices as $device) {

            $userAlias = $device->getUserAlias();
            $typeName = $device->getType()->getName();
            $parameters = $device->getType()->getParameters()->toArray();
            usort($parameters, function($a, $b) {
                return $a->getId() - $b->getId();
            });


            // Gather device details in an array
            $deviceDetails[] = [
                'device' => $device,
                'userAlias' => $userAlias,
                'typeName' => $typeName,
                'parameters' => $parameters,
            ];
        }
        return $this->render('brooker/index.html.twig', [
            'deviceDetails' => $deviceDetails,
        ]);
    }
    #[Route('/updateDeviceParameters', name: 'app_brooker_update', methods: ['POST'])]
    public function updateDeviceParameters(Request $request): Response
    {
        $entityManager = $this->entityManager;
        $formData = $request->get("form");
        $deviceId = $request->get("id");
        // Process and save form data to the database
        // Example: Assuming Device entity and Doctrine EntityManager are used

        // Retrieve the device entity based on the deviceId
        $deviceR = $entityManager->getRepository(Device::class);
        $parameters = $entityManager->getRepository(Parameters::class)->findAll();
        $device = $deviceR->find($deviceId);

        if (!$device) {
            // Handle device not found scenario
            return new Response('Device not found.', Response::HTTP_NOT_FOUND);
        }

        // Update device parameters based on form data
        foreach ($formData as $key => $value) {
            // Check if the form field corresponds to a valid device parameter
            // For example, assuming the form fields match the device properties
            if (is_int($key) && $key > 0)
            {
                $parameter = $entityManager->getRepository(Parameters::class)->find($key);
                $val = $value['value'];
                if (strpos($val, ','))
                {
                    $valArray = explode(',', $val);
                    foreach ($valArray as $valueOfArray)
                    {
                        if (!is_numeric($valueOfArray))
                            return new Response($deviceId, Response::HTTP_FORBIDDEN);

                    }
                    $parameter->setValues($valArray);
                }
                else
                {
                    if (!is_numeric($val))
                        return new Response('Provided wrong value.', Response::HTTP_FORBIDDEN);
                    $parameter->setValues((array)$val);
                }
                $entityManager->persist($parameter);
            }

        }

        // Persist changes to the database
        $entityManager->flush();

        $response = new Response('Success');
        $response->headers->set('X-Device-ID', $deviceId);
        return $response;    // Update only the parameters that were changed in the request

}

}
