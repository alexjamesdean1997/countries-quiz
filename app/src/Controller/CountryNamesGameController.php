<?php

namespace App\Controller;

use App\Entity\Country;
use App\Entity\Game;
use App\Service\CountryService;
use App\Service\Encrypter;
use App\Service\GameService;
use DateTimeImmutable;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
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
        $countries = CountryService::getAll();
        $this->startGame($countries);
        shuffle($countries);

        foreach ($countries as $country){
            $country->encryptedName = Encrypter::encrypt($country->nameFr, 'W0rldQu!z123');
        }

        return $this->render('country-names-game.html.twig', [
            'countries' => $countries,
        ]);
    }

    #[Route('/get-country-info/{countryName}', methods: ['GET'])]
    public function getCountryIso(string $countryName): JsonResponse
    {
        $response = new JsonResponse();
        $country = CountryService::getByNameFr($countryName);

        if (null === $country) {
            $response->setStatusCode(Response::HTTP_NOT_FOUND);
            $response->setContent('Country "' . $countryName . '" not found');
            return $response;
        }

        try{
            $countryInfo = [
                "iso" => strtoupper($country->iso2),
                "name" => $country->nameFr
            ];
            $response->setData($countryInfo);
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

    private function startGame(array $countries): void
    {
        $entityManager  = $this->doctrine->getManager();
        $user           = $this->getUser();
        $gameRepository = $this->doctrine->getRepository(Game::class);
        $gameInProgress = $gameRepository->findOneBy(
            [
                'player' => $user->getId(),
                'state' => GameService::GAME_STATE_IN_PROGRESS
            ]
        );

        if (null !== $gameInProgress) {
            $gameInProgress->setState(GameService::GAME_STATE_FINISHED);
            $gameInProgress->setFinishedAt(new DateTimeImmutable());
            $entityManager->persist($gameInProgress);
            $entityManager->flush();
        }

        $game = new Game();
        $game->setPlayer($user);
        $game->setStartedAt(new DateTimeImmutable());
        $game->setState(GameService::GAME_STATE_IN_PROGRESS);
        $game->setType(GameService::GAME_TYPE_COUNTRY_NAMES);

        foreach ($countries as $country){
            $game->addForgottenCountry($country->iso2);
        }

        $entityManager->persist($game);
        $entityManager->flush();
    }

    #[Route('/country-name-found/{countryName}', methods: ['POST'])]
    public function countryNameFound(string $countryName): Response
    {
        $response = new Response();
        $country = CountryService::getByNameFr($countryName);

        if (null === $country) {
            $response->setStatusCode(Response::HTTP_NOT_FOUND);
            $response->setContent('Country "' . $countryName . '" not found');
            return $response;
        }

        $user = $this->getUser();
        $gameRepository = $this->doctrine->getRepository(Game::class);
        $game = $gameRepository->findOneBy(
            [
                'player' => $user->getId(),
                'state' => GameService::GAME_STATE_IN_PROGRESS
            ]
        );

        if (null === $game) {
            $response->setStatusCode(Response::HTTP_INTERNAL_SERVER_ERROR);
            $response->setContent('The user has no game');
            return $response;
        }

        try{
            $game->removeForgottenCountry($country->iso2);
            $entityManager = $this->doctrine->getManager();
            $entityManager->persist($game);
            $entityManager->flush();
            $response->setContent(count($game->getForgottenCountries()));
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

    #[Route('/stop-country-names-game', methods: ['POST'])]
    public function stopGame(): JsonResponse
    {
        $response       = new JsonResponse();
        $entityManager  = $this->doctrine->getManager();
        $user           = $this->getUser();
        $gameRepository = $this->doctrine->getRepository(Game::class);
        $gameInProgress = $gameRepository->findOneBy(
            [
                'player' => $user->getId(),
                'state' => GameService::GAME_STATE_IN_PROGRESS
            ]
        );

        if (null !== $gameInProgress) {
            $gameInProgress->setState(GameService::GAME_STATE_FINISHED);
            $gameInProgress->setFinishedAt(new DateTimeImmutable());
            $entityManager->persist($gameInProgress);
            $entityManager->flush();
            $response->setStatusCode(Response::HTTP_OK);
            $response->setData($this->buildForgottenCountriesData($gameInProgress));
            return $response;
        }

        $response->setStatusCode(Response::HTTP_INTERNAL_SERVER_ERROR);
        $response->setContent('No games in progress found');
        return $response;
    }

    private function buildForgottenCountriesData(Game $gameInProgress): array
    {
        $forgottenCountries = [];

        foreach ($gameInProgress->getForgottenCountries() as $country) {
            $forgottenCountries[] = [
                "iso" => $country->iso2,
                "name" => $country->nameFr
            ];
        }

        return $forgottenCountries;
    }
}