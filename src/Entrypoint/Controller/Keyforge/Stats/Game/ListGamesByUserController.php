<?php declare(strict_types=1);

namespace AdnanMula\Cards\Entrypoint\Controller\Keyforge\Stats\Game;

use AdnanMula\Cards\Application\Query\Keyforge\Stats\UserStatsQuery;
use AdnanMula\Cards\Entrypoint\Controller\Shared\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

final class ListGamesByUserController extends Controller
{
    public function __invoke(Request $request, string $userId): Response
    {
        $result = $this->extractResult($this->bus->dispatch(new UserStatsQuery($userId)));

        return $this->render(
            'Keyforge/Stats/Game/list_games_by_user.html.twig',
            [
                'games' => $result['games'],
                'reference' => $userId,
                'name' => $this->getReferenceName($result['games'][0] ?? null, $userId),
                'win_rate_vs_users' => $result['win_rate_vs_users'],
                'pick_rate_vs_users' => $result['pick_rate_vs_users'],
                'wins_by_date' => $result['wins_by_date'],
                'losses_by_date' => $result['losses_by_date'],
            ],
        );
    }

    private function getReferenceName(?array $game, string $userId): string
    {
        if (null === $game) {
            return '';
        }

        if ($game['winner'] === $userId) {
            return $game['winner_name'];
        }

        if ($game['loser'] === $userId) {
            return $game['loser_name'];
        }

        return '';
    }
}