<?php declare(strict_types=1);

namespace AdnanMula\Cards\Application\Query\Keyforge\User;

use AdnanMula\Cards\Domain\Model\Keyforge\Deck\KeyforgeDeckUserDataRepository;
use AdnanMula\Cards\Domain\Model\Keyforge\Deck\ValueObject\KeyforgeDeckUserData;
use AdnanMula\Cards\Domain\Model\Keyforge\User\KeyforgeUserRepository;
use AdnanMula\Cards\Domain\Model\Shared\UserRepository;
use AdnanMula\Criteria\Criteria;
use AdnanMula\Criteria\Filter\CompositeFilter;
use AdnanMula\Criteria\Filter\Filter;
use AdnanMula\Criteria\Filter\Filters;
use AdnanMula\Criteria\Filter\FilterType;
use AdnanMula\Criteria\FilterField\FilterField;
use AdnanMula\Criteria\Filter\FilterOperator;
use AdnanMula\Criteria\FilterValue\NullFilterValue;
use AdnanMula\Criteria\FilterValue\StringArrayFilterValue;
use AdnanMula\Criteria\FilterValue\StringFilterValue;

final readonly class GetUsersQueryHandler
{
    public function __construct(
        private UserRepository $userRepository,
        private KeyforgeUserRepository $repository,
        private KeyforgeDeckUserDataRepository $userDataRepository,
    ) {}

    public function __invoke(GetUsersQuery $query): array
    {
        $filters = [];

        if (null !== $query->name) {
            $filters[] = new Filter(new FilterField('name'), new StringFilterValue($query->name), FilterOperator::CONTAINS_INSENSITIVE);
        }

        if (false === $query->withExternal) {
            $filters[] = new Filter(new FilterField('owner'), new NullFilterValue(), FilterOperator::IS_NULL);
        }

        if ($query->onlyFriends) {
            $friends = $this->userRepository->friends($query->userId, false);
            $friendIds = [];

            foreach ($friends as $friend) {
                $friendIds[] = $friend['id'];
                $friendIds[] = $friend['friend_id'];
            }

            $friendIds = array_unique($friendIds);

            $filters[] = new Filter(new FilterField('id'), new StringArrayFilterValue(...$friendIds), FilterOperator::IN);
        }

        $users = $this->repository->search(new Criteria(new Filters(FilterType::AND, ...$filters)));;

        if (false === $query->withGames) {
            return $users;
        }

        $indexedUsers = [];
        $userIds = [];

        foreach ($users as $user) {
            $indexedUsers[$user->id()->value()] = $user;
            $userIds[] = $user->id()->value();
        }

        $userData = $this->userDataRepository->search(
            new Criteria(
                new Filters(
                    FilterType::AND,
                    new Filter(new FilterField('user_id'), new StringArrayFilterValue(...$userIds), FilterOperator::IN),
                ),
            ),
        );

        $indexedUserData = [];

        foreach ($userData as $userDatum) {
            if (null === $userDatum->userId()) {
                continue;
            }

            $indexedUserData[$userDatum->userId()->value()][] = $userDatum;
        }

        $result = [];

        foreach ($indexedUserData as $userId => $userData) {
            if ($query->onlyFriends) {
                $wins = \array_reduce($userData, static fn ($c, KeyforgeDeckUserData $i): int => $c + $i->winsVsFriends());
                $losses = \array_reduce($userData, static fn ($c, KeyforgeDeckUserData $i): int => $c + $i->lossesVsFriends());
            } else {
                $wins = \array_reduce($userData, static fn ($c, KeyforgeDeckUserData $i): int => $c + $i->winsVsUsers());
                $losses = \array_reduce($userData, static fn ($c, KeyforgeDeckUserData $i): int => $c + $i->lossesVsUsers());
            }

            $totalGames = $wins + $losses;

            $winRate = 0;

            if ($totalGames > 0) {
                $winRate = $wins / $totalGames * 100;
            }

            $result[] = [
                'id' => $userId,
                'name' => $indexedUsers[$userId]->name(),
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
}
