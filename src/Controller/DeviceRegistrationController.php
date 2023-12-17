<?php

namespace App\Controller;
use App\Form\ParametersType;
use App\Form\DeviceType;
use App\Entity\Parameters;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Device;
use App\Entity\Type;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Core\Security;

class DeviceRegistrationController extends AbstractController
{
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }


    #[Route('/device_registration', name: 'app_device_registration')]
    public function index(Security $security, UrlGeneratorInterface $urlGenerator,  Request $request): Response
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


        $device = new Device();
        $form = $this->createForm(DeviceType::class, $device);
        $form->handleRequest($request);

       ;

                if ($form->isSubmitted() && $form->isValid() ) {

                    $this->entityManager->persist($device);

                    $this->entityManager->flush();
                return $this->redirectToRoute('app_home_page_logged'); // Redirect to a success page

                }

        if (!$security->getUser()) {
            $url = $urlGenerator->generate('app_home_page');
            return new RedirectResponse($url);
        }

        $loggedUser = $security->getUser();


        return $this->render('device_registration/index.html.twig', [
            'deviceDetails' => $deviceDetails,
            'role' => $loggedUser->getRoles(),
            'form' => $form->createView(),

        ]);
}

}

