<?php

namespace App\Controller;

use App\Entity\Country;
use App\Entity\Game;
use App\Service\GameService;
use DateInterval;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class DashboardController extends AbstractController
{
    private ManagerRegistry $doctrine;

    public function __construct(ManagerRegistry $doctrine) {
        $this->doctrine = $doctrine;
    }

    #[Route('/dashboard', name: 'app_dashboard')]
    public function index(): Response
    {
        $user           = $this->getUser();
        $countries      = $this->doctrine->getRepository(Country::class)->findAll();
        $gameRepository = $this->doctrine->getRepository(Game::class);
        $flagGames      = $gameRepository->findBy(
            [
                'player' => $user->getId(),
                'state'  => GameService::GAME_STATE_FINISHED
            ]
        );

        if ([] === $flagGames) {
            return $this->render('no-games-dashboard.html.twig', [
                'user' => $user
            ]);
        }

        $bestScore     = 0;
        $totalPoints   = 0;
        $totalGameTime = 0;

        foreach ($flagGames as $flagGame) {
            $score = count($countries) - count($flagGame->getForgottenCountries());
            $totalPoints += $score;
            $totalGameTime += $this->getTotalSeconds($flagGame->getDuration());

            if (true === $score > $bestScore) {
                $bestScore = $score;
            }
        }

        $averageGameTime = $totalGameTime / $totalPoints;

        return $this->render('dashboard.html.twig', [
            'average_score_time' => number_format((float)$averageGameTime, 2, '.', ''),
            'best_score'         => $bestScore,
            'countries'          => $countries,
            'flag_games'         => array_reverse($flagGames),
            'user'               => $user
        ]);
    }

    #[Route('/game/{gameId}', name: 'app_game_details')]
    public function gameDetails($gameId): Response
    {
        $user           = $this->getUser();
        $countries      = $this->doctrine->getRepository(Country::class)->findAll();
        $gameRepository = $this->doctrine->getRepository(Game::class);
        $game           = $gameRepository->findOneBy(
            [
                'id'     => $gameId,
                'player' => $user->getId()
            ]
        );

        if (null !== $game->getType()) {
            return $this->flagGameDetails($countries, $game);
        }

        $response = new Response();
        $response->setContent('Game not found');
        return $response;
    }

    private function getTotalSeconds(DateInterval $duration)
    {
        return $duration->s + $duration->i * 60 + $duration->h * 3600;
    }

    private function flagGameDetails(array $countries, Game $game): Response
    {
        $totalGameTime   = $this->getTotalSeconds($game->getDuration());
        $score           = count($countries) - count($game->getForgottenCountries());
        $averageGameTime = $totalGameTime / $score;

        return $this->render('flag-game-details.html.twig', [
            'game' => $game,
            'score' => $score,
            'average_score_time' => number_format((float)$averageGameTime, 2, '.', ''),
            'countries' => $countries,
            'forgotten_countries' => $game->getForgottenCountries(),
        ]);
    }
}