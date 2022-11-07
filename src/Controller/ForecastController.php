<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use App\Form\LocationType;

class ForecastController extends AbstractController
{
    #[Route('/', name: 'app_forecast')]
    public function index(Request $request): Response
    {
        $form = $this->createForm(LocationType::class);
        $form->handleRequest($request);
    
        if ($form->isSubmitted() && $form->isValid())
        {
            
        }

        return $this->renderForm('forecast/index.html.twig', [
            'form' => $form,
        ]);
    }
}
