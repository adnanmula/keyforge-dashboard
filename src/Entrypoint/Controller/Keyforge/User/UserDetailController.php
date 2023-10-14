<?php declare(strict_types=1);

namespace AdnanMula\Cards\Entrypoint\Controller\Keyforge\User;

use AdnanMula\Cards\Application\Query\Keyforge\Stats\UserStatsQuery;
use AdnanMula\Cards\Entrypoint\Controller\Shared\Controller;
use Symfony\Component\HttpFoundation\Response;

final class UserDetailController extends Controller
{
    public function __invoke(string $userId): Response
    {
        $this->assertIsLogged();

        $result = $this->extractResult($this->bus->dispatch(new UserStatsQuery($userId)));

        return $this->render(
            'Keyforge/User/user_detail.html.twig',
            [
                'reference' => $userId,
                'userId' => $userId,
                'name' => $result['username'],
                'user_is_external' => $result['user_is_external'],
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
                'competition_wins' => $result['competition_wins'],
            ],
        );
    }
}
