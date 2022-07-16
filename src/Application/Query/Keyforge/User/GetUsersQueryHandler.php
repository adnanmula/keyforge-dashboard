<?php declare(strict_types=1);

namespace AdnanMula\Cards\Application\Query\Keyforge\User;

use AdnanMula\Cards\Domain\Model\Keyforge\KeyforgeGame;
use AdnanMula\Cards\Domain\Model\Keyforge\KeyforgeGameRepository;
use AdnanMula\Cards\Domain\Model\Keyforge\KeyforgeUser;
use AdnanMula\Cards\Domain\Model\Keyforge\KeyforgeUserRepository;

final class GetUsersQueryHandler
{
    public function __construct(
        private KeyforgeUserRepository $repository,
        private KeyforgeGameRepository $gameRepository,
    ) {}

    public function __invoke(GetUsersQuery $query): array
    {
        $users = $this->repository->all();

        if (false === $query->withGames()) {
            return $users;
        }

        $userIds = \array_map(static fn (KeyforgeUser $user) => $user->id(), $users);

        $games = $this->gameRepository->byUser(...$userIds);

        $result = [];

        foreach ($users as $user) {
            $wins = \count(\array_filter($games, static fn (KeyforgeGame $game) => $game->winner()->equalTo($user->id())));
            $losses = \count(\array_filter($games, static fn (KeyforgeGame $game) => $game->loser()->equalTo($user->id())));

            $totalGames = $wins + $losses;

            $winRate = 0;

            if ($totalGames > 0) {
                $winRate = $wins / $totalGames * 100;
            }

            $result[] = [
                'id' => $user->id()->value(),
                'name' => $user->name(),
                'wins' => $wins,
                'losses' => $losses,
                'win_rate' => $winRate,
                'games_played' => $wins + $losses,
            ];
        }

        \usort($result, static function (array $a, array $b) {
            return $b['wins'] <=> $a['wins'];
        });

        return $result;
    }
}
