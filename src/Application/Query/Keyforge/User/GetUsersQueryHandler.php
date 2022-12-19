<?php declare(strict_types=1);

namespace AdnanMula\Cards\Application\Query\Keyforge\User;

use AdnanMula\Cards\Domain\Model\Keyforge\KeyforgeGame;
use AdnanMula\Cards\Domain\Model\Keyforge\KeyforgeGameRepository;
use AdnanMula\Cards\Domain\Model\Keyforge\KeyforgeUser;
use AdnanMula\Cards\Domain\Model\Keyforge\KeyforgeUserRepository;
use AdnanMula\Cards\Infrastructure\Criteria\Criteria;
use AdnanMula\Cards\Infrastructure\Criteria\Filter\Filter;
use AdnanMula\Cards\Infrastructure\Criteria\Filter\Filters;
use AdnanMula\Cards\Infrastructure\Criteria\Filter\FilterType;
use AdnanMula\Cards\Infrastructure\Criteria\FilterField\FilterField;
use AdnanMula\Cards\Infrastructure\Criteria\FilterValue\FilterOperator;
use AdnanMula\Cards\Infrastructure\Criteria\FilterValue\StringFilterValue;

final class GetUsersQueryHandler
{
    public function __construct(
        private KeyforgeUserRepository $repository,
        private KeyforgeGameRepository $gameRepository,
    ) {}

    public function __invoke(GetUsersQuery $query): array
    {
        //TODO criteria
        $users = $this->repository->all($query->withExternal());

        if (false === $query->withGames()) {
            return $users;
        }

        $userIds = \array_map(static fn (KeyforgeUser $user) => $user->id()->value(), $users);

        $filters = [];

        foreach ($userIds as $userId) {
            $filters[] = new Filter(new FilterField('winner'), new StringFilterValue($userId), FilterOperator::EQUAL);
            $filters[] = new Filter(new FilterField('loser'), new StringFilterValue($userId), FilterOperator::EQUAL);
        }

        $games = $this->gameRepository->search(new Criteria(
            null,
            null,
            null,
            new Filters(FilterType::AND, FilterType::OR, ...$filters),
        ));

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
