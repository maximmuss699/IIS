<?php

namespace App\Controller;
use App\Entity\Device;
use App\Entity\KPI;
use App\Entity\Systems;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
class HomePageLoggedController extends AbstractController
{
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    #[Route('/homepage/Logged', name: 'app_home_page_logged')]
    public function index(Request $request, Security $security, UrlGeneratorInterface $urlGenerator): Response
    {
        $systemRepository = $this->entityManager->getRepository(Systems::class);
        $systems = $systemRepository->findAll();
        $kpiRepository = $this->entityManager->getRepository(KPI::class);
        $kpis = $kpiRepository->findAll();

        if (!$security->getUser()) {
            $url = $urlGenerator->generate('app_home_page');
            return new RedirectResponse($url);
        }

        $loggedUser = $security->getUser();

        $systemKpiPairs = [];
        $devices = [];
        foreach ($systems as $system) {
            $resultF = "";
            foreach ($system->getUsers() as $user) {
                if ($user == $loggedUser) {
                    foreach ($kpis as $kpi) {
                        if ($kpi->getSystems() === $system) {
                            $resultF = $this->calKPI($kpi) ? 'true' : 'false';
                        }
                    }
                    $systemKpiPairs[] = [
                        'system' => $system,
                        'kpi' => $resultF,
                        "Devices" =>   $this->entityManager->getRepository(Device::class)->findBy(['systems' => $system->getId()]),
                    ];
                    break; // Break the loop once the user is found in the system
                }
            }
        }

        return $this->render('home_page_logged/index.html.twig', [
            'systemKpiPairs' => $systemKpiPairs,
            'role' => $loggedUser->getRoles(),
        ]);
    }

    #[Route('/delete-system/{id}', name: 'delete_system')]
    public function deleteSystem(Request $request, int $id, Security $security, UrlGeneratorInterface $urlGenerator ): Response
    {
        $entityManager = $this->entityManager;
        $system = $entityManager->getRepository(Systems::class)->find($id);
        $devices = $entityManager->getRepository(Device::class)->findAll();



        if (!$security->getUser()) {
            $url = $urlGenerator->generate('app_home_page');
            return new RedirectResponse($url);
        }

        $user = $security->getUser();
        if (!$system) {
            throw $this->createNotFoundException('System not found');
        }
        if ($system->getUserOwner() !== $user)
        {
            $system->removeUser($user);
            $entityManager->flush();
            return $this->redirectToRoute('app_home_page_logged');
        }
        foreach ($devices as $device)
        {
            if ($device->getSystems() == $system)
            {
                $device->setSystems(null);
            }
        }

        $kpis = $entityManager->getRepository(KPI::class)->findBy(['systems' => $id]);
        foreach ($kpis as $kpi)
        {
            $kpi->setParameter(null);
            $entityManager->flush();
            $entityManager->remove($kpi);
        }
        $entityManager->flush();
        $entityManager->remove($system);

        $entityManager->flush();
        // Redirect to the forum page or wherever you want after deletion
        return $this->redirectToRoute('app_home_page_logged');
    }


    public function calKPI ($kpi): string
    {
        $values = $kpi->getParameter()->getValues();
        $parVal = null; // Default value if the array is empty
        if (!empty($values)) {
            $lastValueIndex = count($values) - 1;
            $parVal = $values[$lastValueIndex];
            // Or simply: $parVal = end($values);
        }
       switch ($kpi->getFunction())
       {
           case "gt":
               if ($kpi->getValue() < $parVal)
                   return true;
               break;
           case "lt":
               if ($kpi->getValue() > $parVal)
                   return true;
               break;
           case "eq":
               if ($kpi->getValue() == $parVal)
                   return true;
               break;
           case "neq":
               if ($kpi->getValue() != $parVal)
                   return true;
               break;
       }
       return false;
    }
}
