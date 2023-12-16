<?php

namespace App\Controller;
use App\Form\DeviceType;
use App\Entity\Parameters;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Device;
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
    public function index(Security $security, UrlGeneratorInterface $urlGenerator): Response
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

        if (!$security->getUser()) {
            $url = $urlGenerator->generate('app_home_page');
            return new RedirectResponse($url);
        }

        $loggedUser = $security->getUser();


        return $this->render('device_registration/index.html.twig', [
            'deviceDetails' => $deviceDetails,
            'role' => $loggedUser->getRoles()
        ]);
}



      #[Route('/createDevice', name: 'app_device_update', methods: ['POST'])]
      public function updateDeviceParameters(Request $request): Response
      {
          $entityManager = $this->entityManager;
          $formData = $request->get("form");
          $deviceId = $request->get("id");
         $deviceDescription = $request->get('device_description');





          $deviceR = $entityManager->getRepository(Device::class);
          $parameters = $entityManager->getRepository(Parameters::class)->findAll();
          $device = $deviceR->find($deviceId);

          if (!$device) {
              return new Response('Device not found.', Response::HTTP_NOT_FOUND);
          }
          // Update device parameters based on form data
          foreach ($formData as $key => $value) {
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

           $response->headers->set('X-Device-Description', $deviceDescription);
          return $response;    // Update only the parameters that were changed in the request

  }

}
