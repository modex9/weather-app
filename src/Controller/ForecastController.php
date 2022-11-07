<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use App\Form\LocationType;
use App\Repository\LocationRepository;
use App\Service\GeocodingService;
use App\Entity\Location;
use App\Utils\DataKeys;

class ForecastController extends AbstractController
{
    #[Route('/', name: 'app_forecast')]
    public function index(Request $request, GeocodingService $geocodingService, LocationRepository $locationRepository): Response
    {
        $location = new Location();

        $form = $this->createForm(LocationType::class, $location);
        $location = $form->handleRequest($request);
    
        if ($form->isSubmitted() && $form->isValid())
        {
            $location = $form->getData();

            $locationDb = $locationRepository->findBy(
                [
                    'country' => $location->getCountry(),
                    'city' => $location->getCity(),
                ]
            );

            if(!$locationDb)
            {
                $geocodingData = $geocodingService->getGeocodingData($location);
                
                $location->setLatitude($geocodingData[DataKeys::LAT_KEY]);
                $location->setLongitude($geocodingData[DataKeys::LONG_KEY]);
                $locationRepository->save($location, true);
            }
        }

        return $this->renderForm('forecast/index.html.twig', [
            'form' => $form,
        ]);
    }
}
