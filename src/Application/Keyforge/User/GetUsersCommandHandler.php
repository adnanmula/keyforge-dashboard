<?php declare(strict_types=1);

namespace AdnanMula\Cards\Application\Keyforge\User;

use AdnanMula\Cards\Application\Keyforge\Get\GetKeyforgeDecksQuery;
use AdnanMula\Cards\Domain\Model\Keyforge\KeyforgeDeck;
use AdnanMula\Cards\Domain\Model\Keyforge\KeyforgeGame;
use AdnanMula\Cards\Domain\Model\Keyforge\KeyforgeRepository;
use AdnanMula\Cards\Domain\Model\User\User;
use AdnanMula\Cards\Domain\Model\User\UserRepository;

final class GetUsersCommandHandler
{
    public function __construct(
        private UserRepository $repository,
        private KeyforgeRepository $keyforgeRepository
    ) {}

    /** @return array<KeyforgeDeck> */
    public function __invoke(GetUsersCommand $query): array
    {
        $users = $this->repository->all();
        $userIds = \array_map(static fn(User $user) => $user->id(), $users);

        $games = $this->keyforgeRepository->gamesByUser(...$userIds);


        $result = [];

        foreach ($users as $user) {
            $wins = \count(\array_filter($games, static fn (KeyforgeGame $game) => $game->winner()->equalTo($user->id())));
            $loses = \count(\array_filter($games, static fn (KeyforgeGame $game) => $game->loser()->equalTo($user->id())));

            $totalGames = $wins + $loses;

            $winRate = 0;

            if ($totalGames > 0) {
                $winRate = $wins / ($totalGames) * 100;
            }

            $result[] = [
                'id' => $user->id()->value(),
                'name' => $user->name(),
                'wins' => $wins,
                'loses' => $loses,
                'win_rate' => $winRate,
                'games_played' => $wins + $loses,
            ];
        }

        \usort($result, function (array $a, array $b) {
            return $b['wins'] <=> $a['wins'];
        });

        return $result;
    }
}
