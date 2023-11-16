<?php

namespace App\Controller;

use App\Entity\Device;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

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

        $deviceDetails = [];
        foreach ($devices as $device) {
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

        return $this->render('system_details/index.html.twig', [
            'deviceDetails' => $deviceDetails,
            'systemId' => $id,
        ]);
    }
}
