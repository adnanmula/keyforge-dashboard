<?php declare(strict_types=1);

namespace AdnanMula\Cards\Application\Query\Keyforge\Stats;

use AdnanMula\Cards\Domain\Model\Keyforge\KeyforgeCompetition;
use AdnanMula\Cards\Domain\Model\Keyforge\KeyforgeCompetitionRepository;
use AdnanMula\Cards\Domain\Model\Keyforge\KeyforgeDeck;
use AdnanMula\Cards\Domain\Model\Keyforge\KeyforgeDeckRepository;
use AdnanMula\Cards\Domain\Model\Keyforge\KeyforgeGame;
use AdnanMula\Cards\Domain\Model\Keyforge\KeyforgeGameRepository;
use AdnanMula\Cards\Domain\Model\Keyforge\KeyforgeUser;
use AdnanMula\Cards\Domain\Model\Keyforge\KeyforgeUserRepository;
use AdnanMula\Cards\Domain\Model\Keyforge\ValueObject\KeyforgeDeckHouses;
use AdnanMula\Cards\Domain\Model\Keyforge\ValueObject\KeyforgeHouse;
use AdnanMula\Cards\Domain\Model\Keyforge\ValueObject\KeyforgeSet;
use AdnanMula\Cards\Domain\Model\Shared\ValueObject\Uuid;
use AdnanMula\Criteria\Criteria;
use AdnanMula\Criteria\Filter\Filter;
use AdnanMula\Criteria\Filter\Filters;
use AdnanMula\Criteria\Filter\FilterType;
use AdnanMula\Criteria\FilterField\FilterField;
use AdnanMula\Criteria\FilterValue\FilterOperator;
use AdnanMula\Criteria\FilterValue\StringFilterValue;
use AdnanMula\Criteria\Sorting\Order;
use AdnanMula\Criteria\Sorting\OrderType;
use AdnanMula\Criteria\Sorting\Sorting;

final class UserStatsQueryHandler
{
    public function __construct(
        private KeyforgeDeckRepository $deckRepository,
        private KeyforgeGameRepository $gameRepository,
        private KeyforgeUserRepository $userRepository,
        private KeyforgeCompetitionRepository $competitionRepository,
    ) {}

    public function __invoke(UserStatsQuery $query): array
    {
        $games = $this->gameRepository->search(new Criteria(
            new Sorting(
                new Order(new FilterField('date'), OrderType::DESC),
                new Order(new FilterField('created_at'), OrderType::DESC),
            ),
            null,
            null,
            new Filters(
                FilterType::AND,
                FilterType::OR,
                new Filter(new FilterField('winner'), new StringFilterValue($query->userId()->value()), FilterOperator::EQUAL),
                new Filter(new FilterField('loser'), new StringFilterValue($query->userId()->value()), FilterOperator::EQUAL),
            ),
        ));

        $userIds = [];
        $decksIds = [];

        foreach ($games as $game) {
            $userIds[] = $game->winner();
            $userIds[] = $game->loser();
            $decksIds[] = $game->winnerDeck();
            $decksIds[] = $game->loserDeck();
        }

        $decks = $this->deckRepository->byIds(...$decksIds);

        $users = $this->userRepository->byIds(...$userIds);

        $nonExternalUsers = \array_values(\array_filter($users, static fn (KeyforgeUser $user) => $user->external() === false));

        $nonExternalUsersIds = \array_map(static fn (KeyforgeUser $user) => $user->id()->value(), $nonExternalUsers);

        $games = \array_values(\array_filter($games, static function (KeyforgeGame $game) use ($nonExternalUsersIds) {
            return \in_array($game->winner()->value(), $nonExternalUsersIds, true)
                && \in_array($game->loser()->value(), $nonExternalUsersIds, true);
        }));

        $indexedDecks = [];
        $indexedDeckSets = [];
        /** @var array<KeyforgeDeckHouses> $indexedDeckHouses */
        $indexedDeckHouses = [];

        /** @var KeyforgeDeck $deck */
        foreach ($decks as $deck) {
            if (null !== $deck->owner() && $deck->owner()->equalTo($query->userId())) {
                $indexedDecks[$deck->id()->value()] = $deck->name();
            }

            $indexedDeckSets[$deck->id()->value()] = $deck->set()->fullName();
            $indexedDeckHouses[$deck->id()->value()] = $deck->houses();
        }

        $bestAndWorseDecks = [];

        foreach ($indexedDecks as $id => $deck) {
            $bestAndWorseDecks[$id] = [
                'id' => $deck,
                'wins' => 0,
                'losses' => 0,
            ];
        }

        $winsBySet = [
            KeyforgeSet::CotA->fullName() => 0,
            KeyforgeSet::AoA->fullName() => 0,
            KeyforgeSet::WC->fullName() => 0,
            KeyforgeSet::MM->fullName() => 0,
            KeyforgeSet::DT->fullName() => 0,
        ];

        $winsByHouse = [
            KeyforgeHouse::SANCTUM->name => 0,
            KeyforgeHouse::DIS->name => 0,
            KeyforgeHouse::MARS->name => 0,
            KeyforgeHouse::STAR_ALLIANCE->name => 0,
            KeyforgeHouse::SAURIAN->name => 0,
            KeyforgeHouse::SHADOWS->name => 0,
            KeyforgeHouse::UNTAMED->name => 0,
            KeyforgeHouse::BROBNAR->name => 0,
            KeyforgeHouse::UNFATHOMABLE->name => 0,
            KeyforgeHouse::LOGOS->name => 0,
        ];

        $currentWinStreak = 0;
        $longestWinStreak = 0;

        foreach ($games as $game) {
            if ($game->winner()->equalTo($query->userId())) {
                $currentWinStreak++;

                if ($currentWinStreak > $longestWinStreak) {
                    $longestWinStreak = $currentWinStreak;
                }

                $winsBySet[$indexedDeckSets[$game->winnerDeck()->value()]] += 1;
                $houses = $indexedDeckHouses[$game->winnerDeck()->value()];
                $winsByHouse[$houses->value()[0]->value] += 1;
                $winsByHouse[$houses->value()[1]->value] += 1;
                $winsByHouse[$houses->value()[2]->value] += 1;

                if (false === \array_key_exists($game->winnerDeck()->value(), $bestAndWorseDecks)) {
                    continue;
                }

                $bestAndWorseDecks[$game->winnerDeck()->value()] = [
                    'wins' => $bestAndWorseDecks[$game->winnerDeck()->value()]['wins'] + 1,
                    'losses' => $bestAndWorseDecks[$game->winnerDeck()->value()]['losses'],
                ];
            }

            if ($game->loser()->equalTo($query->userId())) {
                $currentWinStreak = 0;

                if (false === \array_key_exists($game->loserDeck()->value(), $bestAndWorseDecks)) {
                    continue;
                }

                $bestAndWorseDecks[$game->loserDeck()->value()] = [
                    'wins' => $bestAndWorseDecks[$game->loserDeck()->value()]['wins'],
                    'losses' => $bestAndWorseDecks[$game->loserDeck()->value()]['losses'] + 1,
                ];
            }
        }

        $indexedUsers = [];

        foreach ($users as $user) {
            $indexedUsers[$user->id()->value()] = $user->name();
        }

        $winRateVsUser = [];

        foreach ($users as $user) {
            if ($user->id()->equalTo($query->userId())) {
                continue;
            }

            $winRateVsUser[$user->id()->value()] = ['wins' => 0, 'losses' => 0];
        }

        $winsByDate = [];
        $lossesByDate = [];

        foreach ($games as $game) {
            $isWin = $game->winner()->equalTo($query->userId());

            if ($isWin) {
                $winRateVsUser[$game->loser()->value()] = [
                    'wins' => $winRateVsUser[$game->loser()->value()]['wins'] + 1,
                    'losses' => $winRateVsUser[$game->loser()->value()]['losses'],
                ];

                $winsByDate[$game->date()->format('Y-m-d')] = isset($winsByDate[$game->date()->format('Y-m-d')])
                    ? $winsByDate[$game->date()->format('Y-m-d')] + 1
                    : 1;
            } else {
                $winRateVsUser[$game->winner()->value()] = [
                    'wins' => $winRateVsUser[$game->winner()->value()]['wins'],
                    'losses' => $winRateVsUser[$game->winner()->value()]['losses'] + 1,
                ];

                $lossesByDate[$game->date()->format('Y-m-d')] = isset($lossesByDate[$game->date()->format('Y-m-d')])
                    ? $lossesByDate[$game->date()->format('Y-m-d')] + 1
                    : 1;
            }
        }

        $resultWinRateByUser = [];
        $resultPickRateByUser = [];
        $resultWinsByUser = [];

        foreach ($winRateVsUser as $userId => $winRate) {
            if (false === \in_array($userId, $nonExternalUsersIds, true)) {
                continue;
            }

            $name = $indexedUsers[$userId];

            $resultWinRateByUser[$name] = $this->winRate($winRate['wins'], $winRate['losses']);
            $resultPickRateByUser[$name] = $this->pickRate($winRate['wins'] + $winRate['losses'], \count($games));
            $resultWinsByUser[$name] = ['wins' => $winRate['wins'], 'losses' => $winRate['losses']];
        }

        $dates = \array_values(\array_unique(\array_merge(\array_keys($winsByDate), \array_keys($lossesByDate))));

        \usort($dates, static function (string $a, string $b) {
            return new \DateTimeImmutable($a) <=> new \DateTimeImmutable($b);
        });

        $resultDates = [];

        foreach ($dates as $date) {
            $resultDates[$date] = 0;
        }

        $resultWinsByDate = $resultDates;
        $resultLossesByDate = $resultDates;

        foreach ($winsByDate as $date => $winByDate) {
            $resultWinsByDate[$date] += $winByDate;
        }

        foreach ($lossesByDate as $date => $lossByDate) {
            $resultLossesByDate[$date] += $lossByDate;
        }

        $bestDeck = [
            'id' => null,
            'wins' => 0,
            'losses' => 0,
            'win_rate' => 0,
            'pick_rate' => 0,
        ];

        $worseDeck = [
            'id' => null,
            'wins' => 0,
            'losses' => 0,
            'win_rate' => 200,
            'pick_rate' => 0,
        ];

        $favoriteDeck = [
            'id' => null,
            'wins' => 0,
            'losses' => 0,
            'win_rate' => 200,
            'pick_rate' => 0,
        ];

        $decksStats = [];

        foreach ($bestAndWorseDecks as $id => $bestAndWorseDeck) {
            $winRate = $this->winRate($bestAndWorseDeck['wins'], $bestAndWorseDeck['losses']);

            if ($winRate > $bestDeck['win_rate']
                || ($winRate === $bestDeck['win_rate'] && $bestAndWorseDeck['wins'] > $bestDeck['wins'])) {
                $bestDeck = [
                    'id' => $id,
                    'name' => $indexedDecks[$id],
                    'wins' => $bestAndWorseDeck['wins'],
                    'losses' => $bestAndWorseDeck['losses'],
                    'win_rate' => $this->winRate($bestAndWorseDeck['wins'], $bestAndWorseDeck['losses']),
                    'pick_rate' => $this->pickRate($bestAndWorseDeck['wins'] + $bestAndWorseDeck['losses'], \count($games)),
                ];
            }

            if (($winRate < $worseDeck['win_rate'] || ($winRate === $worseDeck['win_rate'] && $bestAndWorseDeck['losses'] > $worseDeck['losses']))
                && ($bestAndWorseDeck['wins'] + $bestAndWorseDeck['losses'] !== 0)) {
                $worseDeck = [
                    'id' => $id,
                    'name' => $indexedDecks[$id],
                    'wins' => $bestAndWorseDeck['wins'],
                    'losses' => $bestAndWorseDeck['losses'],
                    'win_rate' => $this->winRate($bestAndWorseDeck['wins'], $bestAndWorseDeck['losses']),
                    'pick_rate' => $this->pickRate($bestAndWorseDeck['wins'] + $bestAndWorseDeck['losses'], \count($games)),
                ];
            }

            $currentTimesPlayed = $bestAndWorseDeck['wins'] + $bestAndWorseDeck['losses'];
            $timesPlayed = $favoriteDeck['wins'] + $favoriteDeck['losses'];

            if ($currentTimesPlayed > $timesPlayed
                || ($timesPlayed === $currentTimesPlayed && $winRate > $favoriteDeck['win_rate'])) {
                $favoriteDeck = [
                    'id' => $id,
                    'name' => $indexedDecks[$id],
                    'wins' => $bestAndWorseDeck['wins'],
                    'losses' => $bestAndWorseDeck['losses'],
                    'win_rate' => $this->winRate($bestAndWorseDeck['wins'], $bestAndWorseDeck['losses']),
                    'pick_rate' => $this->pickRate($bestAndWorseDeck['wins'] + $bestAndWorseDeck['losses'], \count($games)),
                ];
            }

            $decksStats[$indexedDecks[$id]] = [
                'wins' => $bestAndWorseDeck['wins'],
                'losses' => $bestAndWorseDeck['losses'],
                'win_rate' => $winRate,
                'pick_rate' => $this->pickRate($bestAndWorseDeck['wins'] + $bestAndWorseDeck['losses'], \count($games)),
            ];
        }

        $username = '';

        foreach ($users as $user) {
            if ($query->userId()->equalTo($user->id())) {
                $username = $user->name();
            }
        }

        return [
            'username' => $username,
            'win_rate_vs_users' => $resultWinRateByUser,
            'pick_rate_vs_users' => $resultPickRateByUser,
            'wins_by_date' => $resultWinsByDate,
            'losses_by_date' => $resultLossesByDate,
            'wins_vs_users' => $resultWinsByUser,
            'best_deck' => null === $bestDeck['id'] ? null : $bestDeck,
            'worse_deck' => null === $worseDeck['id'] ? null : $worseDeck,
            'favorite_deck' => null === $favoriteDeck['id'] ? null : $favoriteDeck,
            'decks_stats' => $decksStats,
            'wins_by_set' => $winsBySet,
            'wins_by_house' => $winsByHouse,
            'win_streak' => $longestWinStreak,
            'competition_wins' => $this->competitionWins($query->userId()),
        ];
    }

    private function winRate(int $wins, int $losses): float
    {
        $games = $wins + $losses;

        if ($games === 0) {
            return 0;
        }

        return \round($wins / $games * 100, 2);
    }

    private function pickRate(int $picks, int $games): float
    {
        if ($games === 0) {
            return 0;
        }

        return \round($picks / $games * 100, 2);
    }

    private function competitionWins(Uuid $id): array
    {
        $criteria = new Criteria(
            new Sorting(
                new Order(
                    new FilterField('finished_at'),
                    OrderType::ASC,
                ),
            ),
            null,
            null,
            new Filters(
                FilterType::AND,
                FilterType::AND,
                new Filter(new FilterField('winner'), new StringFilterValue($id->value()), FilterOperator::EQUAL),
            ),
        );

        return \array_map(
            static fn (KeyforgeCompetition $competition): array => [
                'name' => $competition->name(),
                'date' => $competition->finishedAt()->format('Y-m-d'),
                'reference' => $competition->reference(),
            ],
            $this->competitionRepository->search($criteria),
        );
    }
}
