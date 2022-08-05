<?php declare(strict_types=1);

namespace AdnanMula\Cards\Entrypoint\Controller\Keyforge\Stats\Game;

use AdnanMula\Cards\Application\Query\Keyforge\Stats\UserStatsQuery;
use AdnanMula\Cards\Entrypoint\Controller\Shared\Controller;
use Symfony\Component\HttpFoundation\Response;

final class ListGamesByUserController extends Controller
{
    public function __invoke(string $userId): Response
    {
        $result = $this->extractResult($this->bus->dispatch(new UserStatsQuery($userId)));

        return $this->render(
            'Keyforge/Stats/Game/list_games_by_user.html.twig',
            [
                'reference' => $userId,
                'userId' => $userId,
                'name' => $this->getReferenceName($result['games'][0] ?? null, $userId),
                'win_rate_vs_users' => $result['win_rate_vs_users'],
                'pick_rate_vs_users' => $result['pick_rate_vs_users'],
                'wins_by_date' => $result['wins_by_date'],
                'losses_by_date' => $result['losses_by_date'],
                'best_deck' => $result['best_deck'],
                'worse_deck' => $result['worse_deck'],
                'favorite_deck' => $result['favorite_deck'],
                'wins_vs_users' => $result['wins_vs_users'],
                'decks_stats' => $result['decks_stats'],
                'wins_by_set' => $result['wins_by_set'],
                'wins_by_house' => $result['wins_by_house'],
                'win_streak' => $result['win_streak'],
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
