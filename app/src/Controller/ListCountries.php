<?php

namespace App\Controller;

use App\Entity\Country;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ListCountries extends AbstractController
{
    #[Route('/countries')]
    public function show(ManagerRegistry $doctrine): Response
    {
        $countries = $doctrine->getRepository(Country::class)->findAll();

        return $this->render('list-countries.html.twig', [
            'countries' => $countries,
        ]);
    }
}