<?php declare(strict_types=1);

namespace AdnanMula\Cards\Application\Command\Keyforge\Stat\User;

use AdnanMula\Cards\Domain\Model\Keyforge\Deck\KeyforgeDeck;
use AdnanMula\Cards\Domain\Model\Keyforge\Deck\KeyforgeDeckRepository;
use AdnanMula\Cards\Domain\Model\Keyforge\Deck\ValueObject\KeyforgeDeckHouses;
use AdnanMula\Cards\Domain\Model\Keyforge\Deck\ValueObject\KeyforgeHouse;
use AdnanMula\Cards\Domain\Model\Keyforge\Deck\ValueObject\KeyforgeSet;
use AdnanMula\Cards\Domain\Model\Keyforge\Game\KeyforgeCompetition;
use AdnanMula\Cards\Domain\Model\Keyforge\Game\KeyforgeCompetitionRepository;
use AdnanMula\Cards\Domain\Model\Keyforge\Game\KeyforgeGameRepository;
use AdnanMula\Cards\Domain\Model\Keyforge\Stat\KeyforgeStat;
use AdnanMula\Cards\Domain\Model\Keyforge\Stat\KeyforgeStatRepository;
use AdnanMula\Cards\Domain\Model\Keyforge\Stat\ValueObject\KeyforgeStatCategory;
use AdnanMula\Cards\Domain\Model\Keyforge\User\KeyforgeUserRepository;
use AdnanMula\Cards\Domain\Model\Shared\UserRepository;
use AdnanMula\Cards\Domain\Model\Shared\ValueObject\UserRole;
use AdnanMula\Cards\Domain\Model\Shared\ValueObject\Uuid;
use AdnanMula\Criteria\Criteria;
use AdnanMula\Criteria\Filter\Filter;
use AdnanMula\Criteria\Filter\FilterType;
use AdnanMula\Criteria\FilterField\FilterField;
use AdnanMula\Criteria\FilterGroup\AndFilterGroup;
use AdnanMula\Criteria\FilterValue\FilterOperator;
use AdnanMula\Criteria\FilterValue\StringArrayFilterValue;
use AdnanMula\Criteria\FilterValue\StringFilterValue;
use AdnanMula\Criteria\Sorting\Order;
use AdnanMula\Criteria\Sorting\OrderType;
use AdnanMula\Criteria\Sorting\Sorting;

final class GenerateUserStatsCommandHandler
{
    public function __construct(
        private KeyforgeStatRepository $statsRepository,
        private UserRepository $userRepository,
        private KeyforgeDeckRepository $deckRepository,
        private KeyforgeGameRepository $gameRepository,
        private KeyforgeUserRepository $keyforgeUserRepository,
        private KeyforgeCompetitionRepository $competitionRepository,
    ) {}

    public function __invoke(GenerateUserStatsCommand $command): void
    {
        if (null === $command->userId) {
            $users = $this->userRepository->byRoles(UserRole::ROLE_ADMIN, UserRole::ROLE_KEYFORGE);
        }

        if (null !== $command->userId) {
            $users = [$this->userRepository->byId($command->userId)];
        }

        foreach ($users as $user) {
            $this->generateForUser($user->id());
        }
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
            null,
            null,
            new Sorting(
                new Order(
                    new FilterField('finished_at'),
                    OrderType::ASC,
                ),
            ),
            new AndFilterGroup(
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

    private function deckStatsByUsers(string ...$ids): array
    {
        $filters = [];

        if (\count($ids) > 0) {
            $filters[] = new AndFilterGroup(
                FilterType::AND,
                new Filter(new FilterField('owner'), new StringArrayFilterValue(...$ids), FilterOperator::IN),
            );
        }

        $filters[] = new AndFilterGroup(
            FilterType::AND,
            new Filter(
                new FilterField('id'),
                new StringArrayFilterValue(
                    '37259b93-1cdd-4ea8-8206-767b071b2643',
                    'dcbc4eae-b03b-4a75-a8ba-65742f1ca1c6',
                    '19ee9a3b-cbe5-4fe5-b4a5-388a1cc3c37a',
                    'eaa1eb19-6ec9-400f-8881-b88eeddd06bc',
                ),
                FilterOperator::NOT_IN,
            ),
        );

        $decks = $this->deckRepository->search(new Criteria(null, null, null, ...$filters));

        $sets = ['CotA', 'AoA', 'WC', 'MM', 'DT', 'WoE', 'GR', 'AS', 'U22', 'M24', 'VM23', 'VM24', 'all'];
        $decksBy = [];

        foreach ($sets as $set) {
            $decksBy[$set] = [
                'houses' => [],
                'sas' => [],
                'expectedAmber' => [],
                'amberControl' => [],
                'creatureControl' => [],
                'artifactControl' => [],
                'creatureCount' => [],
                'artifactCount' => [],
                'efficiency' => [],
                'disruption' => [],
                'recursion' => [],
                'other' => [],
                'effectivePower' => [],
                'bonusAmber' => [],
                'synergy' => [],
                'aerc' => [],
            ];
        }

        foreach ($decks as $deck) {
// @codingStandardsIgnoreStart
            $decksBy[$deck->data()->set->value]['sets'][$deck->data()->set->value] = ($decksBy[$deck->data()->set->value]['sets'][$deck->data()->set->value] ?? 0) + 1;
            $decksBy[$deck->data()->set->value]['houses'][$deck->data()->houses->value()[0]->value] = ($decksBy[$deck->data()->set->value]['houses'][$deck->data()->houses->value()[0]->value] ?? 0) + 1;
            $decksBy[$deck->data()->set->value]['houses'][$deck->data()->houses->value()[1]->value] = ($decksBy[$deck->data()->set->value]['houses'][$deck->data()->houses->value()[1]->value] ?? 0) + 1;
            $decksBy[$deck->data()->set->value]['houses'][$deck->data()->houses->value()[2]->value] = ($decksBy[$deck->data()->set->value]['houses'][$deck->data()->houses->value()[2]->value] ?? 0) + 1;
            $decksBy[$deck->data()->set->value]['sas'][$deck->data()->stats->sas] = ($decksBy[$deck->data()->set->value]['sas'][$deck->data()->stats->sas] ?? 0) + 1;
            $decksBy[$deck->data()->set->value]['expectedAmber'][\round($deck->data()->stats->expectedAmber, 0)] = ($decksBy[$deck->data()->set->value]['expectedAmber'][\round($deck->data()->stats->expectedAmber, 0)] ?? 0) + 1;
            $decksBy[$deck->data()->set->value]['amberControl'][\round($deck->data()->stats->amberControl, 0)] = ($decksBy[$deck->data()->set->value]['amberControl'][\round($deck->data()->stats->amberControl, 0)] ?? 0) + 1;
            $decksBy[$deck->data()->set->value]['creatureControl'][\round($deck->data()->stats->creatureControl, 0)] = ($decksBy[$deck->data()->set->value]['creatureControl'][\round($deck->data()->stats->creatureControl, 0)] ?? 0) + 1;
            $decksBy[$deck->data()->set->value]['artifactControl'][\round($deck->data()->stats->artifactControl, 0)] = ($decksBy[$deck->data()->set->value]['artifactControl'][\round($deck->data()->stats->artifactControl, 0)] ?? 0) + 1;
            $decksBy[$deck->data()->set->value]['creatureCount'][$deck->data()->stats->creatureCount] = ($decksBy[$deck->data()->set->value]['creatureCount'][$deck->data()->stats->creatureCount] ?? 0) + 1;
            $decksBy[$deck->data()->set->value]['artifactCount'][$deck->data()->stats->artifactCount] = ($decksBy[$deck->data()->set->value]['artifactCount'][$deck->data()->stats->artifactCount] ?? 0) + 1;
            $decksBy[$deck->data()->set->value]['efficiency'][$deck->data()->stats->efficiency] = ($decksBy[$deck->data()->set->value]['efficiency'][$deck->data()->stats->efficiency] ?? 0) + 1;
            $decksBy[$deck->data()->set->value]['disruption'][$deck->data()->stats->disruption] = ($decksBy[$deck->data()->set->value]['disruption'][$deck->data()->stats->disruption] ?? 0) + 1;
            $decksBy[$deck->data()->set->value]['recursion'][$deck->data()->stats->recursion] = ($decksBy[$deck->data()->set->value]['recursion'][$deck->data()->stats->recursion] ?? 0) + 1;
            $decksBy[$deck->data()->set->value]['other'][$deck->data()->stats->other] = ($decksBy[$deck->data()->set->value]['other'][$deck->data()->stats->other] ?? 0) + 1;
            $decksBy[$deck->data()->set->value]['effectivePower'][$deck->data()->stats->effectivePower] = ($decksBy[$deck->data()->set->value]['effectivePower'][$deck->data()->stats->effectivePower] ?? 0) + 1;
            $decksBy[$deck->data()->set->value]['bonusAmber'][$deck->data()->stats->rawAmber] = ($decksBy[$deck->data()->set->value]['bonusAmber'][$deck->data()->stats->rawAmber] ?? 0) + 1;
            $decksBy[$deck->data()->set->value]['synergy'][$deck->data()->stats->synergyRating - $deck->data()->stats->antiSynergyRating] = ($decksBy[$deck->data()->set->value]['synergy'][$deck->data()->stats->synergyRating - $deck->data()->stats->antiSynergyRating] ?? 0) + 1;
            $decksBy[$deck->data()->set->value]['aerc'][$deck->data()->stats->aercScore] = ($decksBy[$deck->data()->set->value]['aerc'][$deck->data()->stats->aercScore] ?? 0) + 1;

            $decksBy['all']['sets'][$deck->data()->set->value] = ($decksBy['all']['sets'][$deck->data()->set->value] ?? 0) + 1;
            $decksBy['all']['houses'][$deck->data()->houses->value()[0]->value] = ($decksBy['all']['houses'][$deck->data()->houses->value()[0]->value] ?? 0) + 1;
            $decksBy['all']['houses'][$deck->data()->houses->value()[1]->value] = ($decksBy['all']['houses'][$deck->data()->houses->value()[1]->value] ?? 0) + 1;
            $decksBy['all']['houses'][$deck->data()->houses->value()[2]->value] = ($decksBy['all']['houses'][$deck->data()->houses->value()[2]->value] ?? 0) + 1;
            $decksBy['all']['sas'][$deck->data()->stats->sas] = ($decksBy['all']['sas'][$deck->data()->stats->sas] ?? 0) + 1;
            $decksBy['all']['expectedAmber'][\round($deck->data()->stats->expectedAmber, 0)] = ($decksBy['all']['expectedAmber'][\round($deck->data()->stats->expectedAmber, 0)] ?? 0) + 1;
            $decksBy['all']['amberControl'][\round($deck->data()->stats->amberControl, 0)] = ($decksBy['all']['amberControl'][\round($deck->data()->stats->amberControl, 0)] ?? 0) + 1;
            $decksBy['all']['creatureControl'][\round($deck->data()->stats->creatureControl, 0)] = ($decksBy['all']['creatureControl'][\round($deck->data()->stats->creatureControl, 0)] ?? 0) + 1;
            $decksBy['all']['artifactControl'][\round($deck->data()->stats->artifactControl, 0)] = ($decksBy['all']['artifactControl'][\round($deck->data()->stats->artifactControl, 0)] ?? 0) + 1;
            $decksBy['all']['creatureCount'][$deck->data()->stats->creatureCount] = ($decksBy['all']['creatureCount'][$deck->data()->stats->creatureCount] ?? 0) + 1;
            $decksBy['all']['artifactCount'][$deck->data()->stats->artifactCount] = ($decksBy['all']['artifactCount'][$deck->data()->stats->artifactCount] ?? 0) + 1;
            $decksBy['all']['efficiency'][$deck->data()->stats->efficiency] = ($decksBy['all']['efficiency'][$deck->data()->stats->efficiency] ?? 0) + 1;
            $decksBy['all']['disruption'][$deck->data()->stats->disruption] = ($decksBy['all']['disruption'][$deck->data()->stats->disruption] ?? 0) + 1;
            $decksBy['all']['recursion'][$deck->data()->stats->recursion] = ($decksBy['all']['recursion'][$deck->data()->stats->recursion] ?? 0) + 1;
            $decksBy['all']['other'][$deck->data()->stats->other] = ($decksBy['all']['other'][$deck->data()->stats->other] ?? 0) + 1;
            $decksBy['all']['effectivePower'][$deck->data()->stats->effectivePower] = ($decksBy['all']['effectivePower'][$deck->data()->stats->effectivePower] ?? 0) + 1;
            $decksBy['all']['bonusAmber'][$deck->data()->stats->rawAmber] = ($decksBy['all']['bonusAmber'][$deck->data()->stats->rawAmber] ?? 0) + 1;
            $decksBy['all']['synergy'][$deck->data()->stats->synergyRating - $deck->data()->stats->antiSynergyRating] = ($decksBy['all']['synergy'][$deck->data()->stats->synergyRating - $deck->data()->stats->antiSynergyRating] ?? 0) + 1;
            $decksBy['all']['aerc'][$deck->data()->stats->aercScore] = ($decksBy['all']['aerc'][$deck->data()->stats->aercScore] ?? 0) + 1;
// @codingStandardsIgnoreEnd
        }

        return $decksBy;
    }

    private function deckStats2ByUsers(string ...$ids): array
    {
        $filters = [];

        if (\count($ids) > 0) {
            $filters[] = new AndFilterGroup(
                FilterType::AND,
                new Filter(new FilterField('owner'), new StringArrayFilterValue(...$ids), FilterOperator::IN),
            );
        }

        $filters[] = new AndFilterGroup(
            FilterType::AND,
            new Filter(
                new FilterField('id'),
                new StringArrayFilterValue(
                    '37259b93-1cdd-4ea8-8206-767b071b2643',
                    'dcbc4eae-b03b-4a75-a8ba-65742f1ca1c6',
                    '19ee9a3b-cbe5-4fe5-b4a5-388a1cc3c37a',
                    'eaa1eb19-6ec9-400f-8881-b88eeddd06bc',
                ),
                FilterOperator::NOT_IN,
            ),
        );

        $decks = $this->deckRepository->search(new Criteria(null, null, null, ...$filters));
        $decks = \array_map(static function (KeyforgeDeck $d) {
            return [
                'id' => $d->id()->value(),
                'name' => $d->data()->name,
                'set' => $d->data()->set->value,
                'stats' => [
                    'sas' => $d->data()->stats->sas,
                    'expectedAmber' => $d->data()->stats->expectedAmber,
                    'amberControl' => $d->data()->stats->amberControl,
                    'creatureControl' => $d->data()->stats->creatureControl,
                    'artifactControl' => $d->data()->stats->artifactControl,
                    'creatureCount' => $d->data()->stats->creatureCount,
                    'artifactCount' => $d->data()->stats->artifactCount,
                    'efficiency' => $d->data()->stats->efficiency,
                    'disruption' => $d->data()->stats->disruption,
                    'recursion' => $d->data()->stats->recursion,
                    'other' => $d->data()->stats->other,
                    'effectivePower' => $d->data()->stats->effectivePower,
                    'bonusAmber' => $d->data()->stats->rawAmber,
                    'synergy' => $d->data()->stats->synergyRating - $d->data()->stats->antiSynergyRating,
                    'aerc' => $d->data()->stats->aercScore,
                ],
            ];
        }, $decks);

        $amountToSave = 10;

        $sets = ['CotA', 'AoA', 'WC', 'MM', 'DT', 'WoE', 'GR', 'AS', 'U22', 'M24', 'VM23', 'VM24', 'all'];

        $result = [];

        $statsNames = ['sas', 'expectedAmber', 'amberControl', 'creatureControl', 'artifactControl', 'creatureCount', 'artifactCount',
            'efficiency', 'disruption', 'recursion', 'other', 'effectivePower', 'bonusAmber', 'synergy', 'aerc'];

        foreach ($sets as $set) {
            $setDecks = 'all' === $set
                ? $decks
                : \array_values(\array_filter($decks, static fn (array $d) => $d['set'] === $set));

            $result[$set] = [];

            foreach ($statsNames as $statsName) {
                \usort($setDecks, static function (array $a, array $b) use ($statsName) {
                    return $b['stats'][$statsName] <=> $a['stats'][$statsName];
                });

                $result[$set][$statsName] = \array_slice($setDecks, 0, $amountToSave);
            }
        }

        return $result;
    }

    public function generateForUser(Uuid $userIdToGenerate): void
    {
        $appUser = $this->userRepository->byId($userIdToGenerate);
        $kfUser = $this->keyforgeUserRepository->byId($userIdToGenerate);

        $friends = $this->userRepository->friends($userIdToGenerate);
        $friendsIds = \array_map(static fn (array $f) => $f['id'], $friends);
        $friendsIdsWithoutUser = array_filter($friendsIds, static fn (string $id) => $id !== $kfUser->id()->value());

        $draftDecks = [
            '37259b93-1cdd-4ea8-8206-767b071b2643',
            'dcbc4eae-b03b-4a75-a8ba-65742f1ca1c6',
            '19ee9a3b-cbe5-4fe5-b4a5-388a1cc3c37a',
            'eaa1eb19-6ec9-400f-8881-b88eeddd06bc',
        ];

        $games = $this->gameRepository->search(new Criteria(
            null,
            null,
            new Sorting(
                new Order(new FilterField('date'), OrderType::DESC),
                new Order(new FilterField('created_at'), OrderType::DESC),
            ),
            new AndFilterGroup(
                FilterType::OR,
                new Filter(new FilterField('winner'), new StringFilterValue($userIdToGenerate->value()), FilterOperator::EQUAL),
                new Filter(new FilterField('loser'), new StringFilterValue($userIdToGenerate->value()), FilterOperator::EQUAL),
            ),
            new AndFilterGroup(
                FilterType::OR,
                new Filter(new FilterField('winner'), new StringArrayFilterValue(...$friendsIds), FilterOperator::IN),
                new Filter(new FilterField('loser'), new StringArrayFilterValue(...$friendsIds), FilterOperator::IN),
            ),
            new AndFilterGroup(
                FilterType::AND,
                new Filter(new FilterField('winner_deck'), new StringArrayFilterValue(...$draftDecks), FilterOperator::NOT_IN),
                new Filter(new FilterField('loser_deck'), new StringArrayFilterValue(...$draftDecks), FilterOperator::NOT_IN),
            ),
        ));

        $userIds = [];
        $decksIds = [];

        foreach ($games as $game) {
            $userIds[] = $game->winner();
            $userIds[] = $game->loser();

            if (false === \in_array($game->winnerDeck()->value(), $draftDecks, true)) {
                $decksIds[] = $game->winnerDeck();
            }

            if (false === \in_array($game->loserDeck()->value(), $draftDecks, true)) {
                $decksIds[] = $game->loserDeck();
            }
        }

        $decks = $this->deckRepository->byIds(...$decksIds);

        $users = $this->keyforgeUserRepository->byIds(...$userIds);

        $nonExternalUsersIds = $friendsIds;

        $indexedDecks = [];
        $indexedDeckSets = [];
        /** @var array<KeyforgeDeckHouses> $indexedDeckHouses */
        $indexedDeckHouses = [];

        /** @var KeyforgeDeck $deck */
        foreach ($decks as $deck) {
            if (null !== $deck->userData()->owner && $deck->userData()->owner->equalTo($userIdToGenerate)) {
                $indexedDecks[$deck->id()->value()] = $deck->data()->name;
            }

            $indexedDeckSets[$deck->id()->value()] = $deck->data()->set->fullName();
            $indexedDeckHouses[$deck->id()->value()] = $deck->data()->houses;
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
            KeyforgeSet::WoE->fullName() => 0,
            KeyforgeSet::U22->fullName() => 0,
            KeyforgeSet::VM23->fullName() => 0,
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
            KeyforgeHouse::EKWIDON->name => 0,
        ];

        $currentWinStreak = 0;
        $longestWinStreak = 0;

        foreach ($games as $game) {
            if ($game->winner()->equalTo($userIdToGenerate)) {
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

            if ($game->loser()->equalTo($userIdToGenerate)) {
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
            if ($user->id()->equalTo($userIdToGenerate)) {
                continue;
            }

            $winRateVsUser[$user->id()->value()] = ['wins' => 0, 'losses' => 0];
        }

        $winsByDate = [];
        $lossesByDate = [];

        foreach ($games as $game) {
            if ($game->isSoloPlay()) {
                continue;
            }

            $isWin = $game->winner()->equalTo($userIdToGenerate);

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

        $this->statsRepository->remove(KeyforgeStatCategory::USER_PROFILE, $kfUser->id());
        $this->statsRepository->save(new KeyforgeStat(
            Uuid::v4(),
            KeyforgeStatCategory::USER_PROFILE,
            $kfUser->id(),
            [
                'user_id' => $kfUser->id()->value(),
                'username' => $kfUser->name(),
                'user_is_external' => null === $appUser,
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
                'competition_wins' => $this->competitionWins($userIdToGenerate),
                'deck_tops' => [
                    'amounts' => [
                        'user' => $this->deckStatsByUsers($kfUser->id()->value()),
                        'friends' => $this->deckStatsByUsers(...$friendsIdsWithoutUser),
                        'all' => $this->deckStatsByUsers(),
                    ],
                    'by_stats' => [
                        'user' => $this->deckStats2ByUsers($kfUser->id()->value()),
                        'friends' => $this->deckStats2ByUsers(...$friendsIdsWithoutUser),
                        'all' => $this->deckStats2ByUsers(),
                    ],
                ],
            ],
        ));
    }
}
