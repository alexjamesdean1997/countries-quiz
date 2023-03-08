<?php

namespace App\Controller;

use App\Service\CountryService;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ListCountriesController extends AbstractController
{
    #[Route('/', name: 'app_list_countries')]
    public function show(ManagerRegistry $doctrine): Response
    {
        $countries = CountryService::getAll();

        return $this->render('list-countries.html.twig', [
            'countries' => $countries,
        ]);
    }
}