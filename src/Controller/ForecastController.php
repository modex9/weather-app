<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use App\Form\LocationType;
use App\Repository\LocationRepository;
use App\Service\WeatherForecastService;
use App\Service\GeocodingService;
use App\Entity\Location;
use App\Utils\DataKeys;

class ForecastController extends AbstractController
{
    #[Route('/', name: 'app_forecast')]
    public function index(Request $request, GeocodingService $geocodingService, LocationRepository $locationRepository, WeatherForecastService $weatherForecastService): Response
    {
        $location = new Location();

        $form = $this->createForm(LocationType::class, $location);
        $location = $form->handleRequest($request);
    
        $forecastTemperature = null;
        if ($form->isSubmitted() && $form->isValid())
        {
            $location = $form->getData();

            $locationDb = $locationRepository->findOneBy(
                [
                    'country' => $location->getCountry(),
                    'city' => $location->getCity(),
                ]
            );

            if($locationDb)
                $location = $locationDb;

            if(!$locationDb || $location->getLatitude() === null || $location->getLongitude() === null)
            {
                $geocodingData = $geocodingService->getGeocodingData($location);
                
                $latitude = $geocodingData[DataKeys::LAT_KEY];
                $longitude = $geocodingData[DataKeys::LONG_KEY];
                $location->setLatitude($latitude);
                $location->setLongitude($longitude);
            }

            $latitude = $location->getLatitude();
            $longitude = $location->getLongitude();
            $forecastTemperature = $weatherForecastService->getWeatherForecast($latitude, $longitude);
            $location->setTemperature($forecastTemperature);
            $locationRepository->save($location, true);
        }

        return $this->renderForm('forecast/index.html.twig', [
            'form' => $form,
            'location' => $location,
            'forecastTemperature' => $forecastTemperature, 
        ]);
    }
}
