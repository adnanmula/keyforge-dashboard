<?php declare(strict_types=1);

namespace AdnanMula\Cards\Application\Query\Keyforge\User;

use AdnanMula\Cards\Domain\Model\Keyforge\KeyforgeGame;
use AdnanMula\Cards\Domain\Model\Keyforge\KeyforgeGameRepository;
use AdnanMula\Cards\Domain\Model\Keyforge\KeyforgeUser;
use AdnanMula\Cards\Domain\Model\Keyforge\KeyforgeUserRepository;
use AdnanMula\Cards\Domain\Model\Shared\UserRepository;
use AdnanMula\Criteria\Criteria;
use AdnanMula\Criteria\Filter\Filter;
use AdnanMula\Criteria\Filter\FilterType;
use AdnanMula\Criteria\FilterField\FilterField;
use AdnanMula\Criteria\FilterGroup\AndFilterGroup;
use AdnanMula\Criteria\FilterValue\FilterOperator;
use AdnanMula\Criteria\FilterValue\StringFilterValue;

final class GetUsersQueryHandler
{
    public function __construct(
        private UserRepository $userRepository,
        private KeyforgeUserRepository $repository,
        private KeyforgeGameRepository $gameRepository,
    ) {}

    public function __invoke(GetUsersQuery $query): array
    {
        $users = $this->repository->search(new Criteria(null, null, null));

        if ($query->onlyFriends) {
            $friends = $this->userRepository->friends($query->userId);
            $friendsId = \array_map(static fn (array $f) => $f['id'], $friends);
            $friendsId[] = $query->userId->value();

            $users = \array_filter($users, static fn (KeyforgeUser $u) => \in_array($u->id()->value(), $friendsId, true));
        }

        if (false === $query->withGames) {
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
            new AndFilterGroup(FilterType::OR, ...$filters),
        ));

        if ($query->onlyFriends) {
            $games = $this->excludeGamesWithNotFriends($users, $games);
        }

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
                'is_external' => false,
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

    private function excludeGamesWithNotFriends(array $users, array $games): array
    {
        $userIds = \array_map(static fn (KeyforgeUser $user) => $user->id()->value(), $users);

        return \array_values(\array_filter($games, static function (KeyforgeGame $game) use ($userIds) {
            return \in_array($game->winner()->value(), $userIds, true)
                && \in_array($game->loser()->value(), $userIds, true);
        }));
    }
}
