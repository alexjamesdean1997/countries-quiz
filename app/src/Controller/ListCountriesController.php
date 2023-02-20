<?php

namespace App\Controller;

use App\Entity\Country;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ListCountriesController extends AbstractController
{
    #[Route('/', name: 'app_list_countries')]
    public function show(ManagerRegistry $doctrine): Response
    {
        $countries = $doctrine->getRepository(Country::class)->findAll();

        $data = file_get_contents('./../data/countries.json');
        $jsonData = json_decode($data,1);
        dd($jsonData);

        return $this->render('list-countries.html.twig', [
            'countries' => $countries,
        ]);
    }
}