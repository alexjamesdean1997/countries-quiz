<?php

namespace App\Controller;

use App\Entity\Country;
use App\Entity\Game;
use App\Service\Encrypter;
use App\Service\GameService;
use DateTimeImmutable;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class CountryNamesGameController extends AbstractController
{

    private ManagerRegistry $doctrine;

    public function __construct(ManagerRegistry $doctrine) {
        $this->doctrine = $doctrine;
    }

    #[Route('/country-names-game', name: 'app_country_names_game')]
    public function show(): Response
    {
        $countries = $this->doctrine->getRepository(Country::class)->findAll();
        shuffle($countries);

        foreach ($countries as $country){
            $encryptedName = Encrypter::encrypt($country->getName(), 'W0rldQu!z123');
            $country->setEncryptedName($encryptedName);
        }

        return $this->render('country-names-game.html.twig', [
            'countries' => $countries,
        ]);
    }

    #[Route('/get-country-iso/{countryName}', methods: ['GET'])]
    public function getCountryIso(string $countryName): Response
    {
        $response = new Response();
        $country = $this->doctrine->getRepository(Country::class)->findOneByName($countryName);

        if (null === $country) {
            $response->setStatusCode(Response::HTTP_NOT_FOUND);
            $response->setContent('Country "' . $countryName . '" not found');
            return $response;
        }

        try{
            $response->setContent(strtoupper($country->getFlagImgCode()));
            $response->setStatusCode(Response::HTTP_OK);
            return $response;
        }
        catch(\Exception $e){
            error_log($e->getMessage());
            $response->setStatusCode(Response::HTTP_INTERNAL_SERVER_ERROR);
            $response->setContent($e->getMessage());
            return $response;
        }
    }
}