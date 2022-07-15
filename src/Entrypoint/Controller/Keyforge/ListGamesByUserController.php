<?php declare(strict_types=1);

namespace AdnanMula\Cards\Entrypoint\Controller\Keyforge;

use AdnanMula\Cards\Application\Keyforge\UserStats\GetUserStatsQuery;
use AdnanMula\Cards\Entrypoint\Controller\Shared\QueryController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

final class ListGamesByUserController extends QueryController
{
    public function __invoke(Request $request, string $userId): Response
    {
        $result = $this->extractResult($this->bus->dispatch(new GetUserStatsQuery($userId)));

        return $this->render(
            'Keyforge/list_games_by_user.html.twig',
            [
                'games' => $result['games'],
                'reference' => $userId,
                'name' => $this->getReferenceName($result['games'][0] ?? null, $userId),
                'win_rate_vs_users' => $result['win_rate_vs_users'],
                'pick_rate_vs_users' => $result['pick_rate_vs_users'],
                'wins_by_date' => $result['wins_by_date'],
                'loses_by_date' => $result['loses_by_date'],
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
