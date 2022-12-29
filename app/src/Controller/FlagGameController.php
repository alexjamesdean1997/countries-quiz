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

class FlagGameController extends AbstractController
{

    private ManagerRegistry $doctrine;

    public function __construct(ManagerRegistry $doctrine) {
        $this->doctrine = $doctrine;
    }

    #[Route('/flag-game')]
    public function show(ManagerRegistry $doctrine): Response
    {
        $countries = $doctrine->getRepository(Country::class)->findAll();
        $this->startGame($countries);
        shuffle($countries);

        foreach ($countries as $country){
            $encryptedName = Encrypter::encrypt($country->getName(), 'W0rldQu!z123');
            $country->setEncryptedName($encryptedName);
        }

        return $this->render('flag-game.html.twig', [
            'countries' => $countries,
        ]);
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
        $game->setType(GameService::GAME_TYPE_FLAGS);

        foreach ($countries as $country){
            $game->addForgottenCountry($country);
        }

        $entityManager->persist($game);
        $entityManager->flush();
    }

    #[Route('/flag-found/{countryName}')]
    public function flagGuessed(string $countryName): Response
    {
        $response = new Response();
        $country = $this->doctrine->getRepository(Country::class)->findOneByName($countryName);
        $user = $this->getUser();
        $game = $user->getLastGame();
        $game->removeForgottenCountry($country);
        $entityManager = $this->doctrine->getManager();
        $entityManager->persist($game);
        $entityManager->flush();
        $response->setStatusCode(Response::HTTP_OK);
        return $response;
    }
}