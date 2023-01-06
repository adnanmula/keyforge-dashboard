<?php declare(strict_types=1);

namespace AdnanMula\Cards\Application\Query\Keyforge\Stats;

use AdnanMula\Cards\Domain\Model\Keyforge\KeyforgeCompetitionRepository;
use AdnanMula\Cards\Domain\Model\Keyforge\KeyforgeDeckRepository;
use AdnanMula\Cards\Domain\Model\Keyforge\KeyforgeGameRepository;
use AdnanMula\Cards\Domain\Model\Keyforge\ValueObject\KeyforgeHouse;
use AdnanMula\Cards\Domain\Model\Keyforge\ValueObject\KeyforgeSet;
use AdnanMula\Cards\Infrastructure\Criteria\Criteria;
use AdnanMula\Cards\Infrastructure\Criteria\Filter\Filter;
use AdnanMula\Cards\Infrastructure\Criteria\Filter\Filters;
use AdnanMula\Cards\Infrastructure\Criteria\Filter\FilterType;
use AdnanMula\Cards\Infrastructure\Criteria\FilterField\FilterField;
use AdnanMula\Cards\Infrastructure\Criteria\FilterValue\FilterOperator;
use AdnanMula\Cards\Infrastructure\Criteria\FilterValue\NullFilterValue;

final class GeneralStatsQueryHandler
{
    public function __construct(
        private KeyforgeDeckRepository $deckRepository,
        private KeyforgeGameRepository $gameRepository,
        private KeyforgeCompetitionRepository $competitionRepository,
    ) {}

    public function __invoke(GeneralStatsQuery $query): array
    {
        $decks = $this->deckRepository->search(
            new Criteria(
                null,
                null,
                null,
                new Filters(
                    FilterType::AND,
                    FilterType::AND,
                    new Filter(new FilterField('owner'), new NullFilterValue(), FilterOperator::IS_NOT_NULL),
                ),
            ),
        );

        [$setWins, $houseWins, $houseLosses, $housePresence, $setLosses, $setPresence] = $this->initializeCounters();

        $maxSas = 0;
        $maxSasResult = [];
        $minSas = 1000;
        $minSasResult = [];
        $maxWinRate = 0;
        $maxWinRateResult = [];

        $indexedDecks = [];

        foreach ($decks as $deck) {
            $setPresence[$deck->set()->name]++;

            foreach ($deck->houses()->value() as $house) {
                $housePresence[$house->name]++;
            }

            $winRate = $this->winRate($deck->wins(), $deck->losses());

            if ($deck->sas() > $maxSas) {
                $maxSas = $deck->sas();
                $maxSasResult = [
                    'id' => $deck->id()->value(),
                    'name' => $deck->name(),
                    'sas' => $deck->sas(),
                    'wins' => $deck->wins(),
                    'losses' => $deck->losses(),
                    'win_rate' => $this->winRate($deck->wins(), $deck->losses()),
                ];
            }

            if ($deck->sas() < $minSas) {
                $minSas = $deck->sas();
                $minSasResult = [
                    'id' => $deck->id()->value(),
                    'name' => $deck->name(),
                    'sas' => $deck->sas(),
                    'wins' => $deck->wins(),
                    'losses' => $deck->losses(),
                    'win_rate' => $winRate,
                ];
            }

            if ($winRate > $maxWinRate) {
                $maxWinRate = $winRate;
                $maxWinRateResult = [
                    'id' => $deck->id()->value(),
                    'name' => $deck->name(),
                    'sas' => $deck->sas(),
                    'wins' => $deck->wins(),
                    'losses' => $deck->losses(),
                    'win_rate' => $this->winRate($deck->wins(), $deck->losses()),
                ];
            }

            $indexedDecks[$deck->id()->value()] = $deck;
        }

        $games = $this->gameRepository->all(null);

        $gamesCount = 0;

        foreach ($games as $game) {
            if (false === \array_key_exists($game->winnerDeck()->value(), $indexedDecks)) {
                continue;
            }

            if (false === \array_key_exists($game->loserDeck()->value(), $indexedDecks)) {
                continue;
            }

            $winnerDeck = $indexedDecks[$game->winnerDeck()->value()];

            $setWins[$winnerDeck->set()->name]++;

            foreach ($winnerDeck->houses()->value() as $house) {
                $houseWins[$house->name]++;
            }

            $loserDeck = $indexedDecks[$game->loserDeck()->value()];

            $setLosses[$loserDeck->set()->name]++;

            foreach ($loserDeck->houses()->value() as $house) {
                $houseLosses[$house->name]++;
            }

            $gamesCount++;
        }

        $setWinRate = [
            KeyforgeSet::CotA->name => $this->winRate($setWins[KeyforgeSet::CotA->name], $setLosses[KeyforgeSet::CotA->name]),
            KeyforgeSet::AoA->name => $this->winRate($setWins[KeyforgeSet::AoA->name], $setLosses[KeyforgeSet::AoA->name]),
            KeyforgeSet::WC->name => $this->winRate($setWins[KeyforgeSet::WC->name], $setLosses[KeyforgeSet::WC->name]),
            KeyforgeSet::MM->name => $this->winRate($setWins[KeyforgeSet::MM->name], $setLosses[KeyforgeSet::MM->name]),
            KeyforgeSet::DT->name => $this->winRate($setWins[KeyforgeSet::DT->name], $setLosses[KeyforgeSet::DT->name]),
        ];

        $houseWinRate = [
            KeyforgeHouse::SANCTUM->name => $this->winRate($houseWins[KeyforgeHouse::SANCTUM->name], $houseLosses[KeyforgeHouse::SANCTUM->name]),
            KeyforgeHouse::DIS->name => $this->winRate($houseWins[KeyforgeHouse::DIS->name], $houseLosses[KeyforgeHouse::DIS->name]),
            KeyforgeHouse::MARS->name => $this->winRate($houseWins[KeyforgeHouse::MARS->name], $houseLosses[KeyforgeHouse::MARS->name]),
            KeyforgeHouse::STAR_ALLIANCE->name => $this->winRate($houseWins[KeyforgeHouse::STAR_ALLIANCE->name], $houseLosses[KeyforgeHouse::STAR_ALLIANCE->name]),
            KeyforgeHouse::SAURIAN->name => $this->winRate($houseWins[KeyforgeHouse::SAURIAN->name], $houseLosses[KeyforgeHouse::SAURIAN->name]),
            KeyforgeHouse::SHADOWS->name => $this->winRate($houseWins[KeyforgeHouse::SHADOWS->name], $houseLosses[KeyforgeHouse::SHADOWS->name]),
            KeyforgeHouse::UNTAMED->name => $this->winRate($houseWins[KeyforgeHouse::UNTAMED->name], $houseLosses[KeyforgeHouse::UNTAMED->name]),
            KeyforgeHouse::BROBNAR->name => $this->winRate($houseWins[KeyforgeHouse::BROBNAR->name], $houseLosses[KeyforgeHouse::BROBNAR->name]),
            KeyforgeHouse::UNFATHOMABLE->name => $this->winRate($houseWins[KeyforgeHouse::UNFATHOMABLE->name], $houseLosses[KeyforgeHouse::UNFATHOMABLE->name]),
            KeyforgeHouse::LOGOS->name => $this->winRate($houseWins[KeyforgeHouse::LOGOS->name], $houseLosses[KeyforgeHouse::LOGOS->name]),
        ];

        $totalSetPicks = $gamesCount * 2;
        $totalHousePicks = $gamesCount * 6;

        $setPickRate = [
            KeyforgeSet::CotA->name => $this->pickRate($setWins[KeyforgeSet::CotA->name] + $setLosses[KeyforgeSet::CotA->name], $totalSetPicks),
            KeyforgeSet::AoA->name => $this->pickRate($setWins[KeyforgeSet::AoA->name] + $setLosses[KeyforgeSet::AoA->name], $totalSetPicks),
            KeyforgeSet::WC->name => $this->pickRate($setWins[KeyforgeSet::WC->name] + $setLosses[KeyforgeSet::WC->name], $totalSetPicks),
            KeyforgeSet::MM->name => $this->pickRate($setWins[KeyforgeSet::MM->name] + $setLosses[KeyforgeSet::MM->name], $totalSetPicks),
            KeyforgeSet::DT->name => $this->pickRate($setWins[KeyforgeSet::DT->name] + $setLosses[KeyforgeSet::DT->name], $totalSetPicks),
        ];

        $housePickRate = [
            KeyforgeHouse::SANCTUM->name => $this->pickRate($houseWins[KeyforgeHouse::SANCTUM->name] + $houseLosses[KeyforgeHouse::SANCTUM->name], $totalHousePicks),
            KeyforgeHouse::DIS->name => $this->pickRate($houseWins[KeyforgeHouse::DIS->name] + $houseLosses[KeyforgeHouse::DIS->name], $totalHousePicks),
            KeyforgeHouse::MARS->name => $this->pickRate($houseWins[KeyforgeHouse::MARS->name] + $houseLosses[KeyforgeHouse::MARS->name], $totalHousePicks),
            KeyforgeHouse::STAR_ALLIANCE->name => $this->pickRate($houseWins[KeyforgeHouse::STAR_ALLIANCE->name] + $houseLosses[KeyforgeHouse::STAR_ALLIANCE->name], $totalHousePicks),
            KeyforgeHouse::SAURIAN->name => $this->pickRate($houseWins[KeyforgeHouse::SAURIAN->name] + $houseLosses[KeyforgeHouse::SAURIAN->name], $totalHousePicks),
            KeyforgeHouse::SHADOWS->name => $this->pickRate($houseWins[KeyforgeHouse::SHADOWS->name] + $houseLosses[KeyforgeHouse::SHADOWS->name], $totalHousePicks),
            KeyforgeHouse::UNTAMED->name => $this->pickRate($houseWins[KeyforgeHouse::UNTAMED->name] + $houseLosses[KeyforgeHouse::UNTAMED->name], $totalHousePicks),
            KeyforgeHouse::BROBNAR->name => $this->pickRate($houseWins[KeyforgeHouse::BROBNAR->name] + $houseLosses[KeyforgeHouse::BROBNAR->name], $totalHousePicks),
            KeyforgeHouse::UNFATHOMABLE->name => $this->pickRate($houseWins[KeyforgeHouse::UNFATHOMABLE->name] + $houseLosses[KeyforgeHouse::UNFATHOMABLE->name], $totalHousePicks),
            KeyforgeHouse::LOGOS->name => $this->pickRate($houseWins[KeyforgeHouse::LOGOS->name] + $houseLosses[KeyforgeHouse::LOGOS->name], $totalHousePicks),
        ];

        $competitionCount = $this->competitionRepository->count(new Criteria(null, null, null));

        return [
            'deck_count' => \count($decks),
            'set_presence' => $setPresence,
            'house_presence' => $housePresence,
            'games_played' => $gamesCount,
            'deck_win_rate' => $maxWinRateResult,
            'deck_max_sas' => $maxSasResult,
            'deck_min_sas' => $minSasResult,
            'set_wins' => $setWins,
            'house_wins' => $houseWins,
            'set_win_rate' => $setWinRate,
            'house_win_rate' => $houseWinRate,
            'set_pick_rate' => $setPickRate,
            'house_pick_rate' => $housePickRate,
            'competition_count' => $competitionCount,
            'player_count' => '🥚🥚🥚🥚🥚🥚',
        ];
    }

    private function winRate(int $wins, int $losses): float
    {
        $games = $wins + $losses;

        if ($games === 0) {
            return 0;
        }

        return \round($wins/$games*100, 2);
    }

    private function pickRate(int $picks, int $games): float
    {
        if ($games === 0) {
            return 0;
        }

        return \round($picks / $games * 100, 2);
    }

    private function initializeCounters(): array
    {
        $setWins = [
            KeyforgeSet::CotA->name => 0,
            KeyforgeSet::AoA->name => 0,
            KeyforgeSet::WC->name => 0,
            KeyforgeSet::MM->name => 0,
            KeyforgeSet::DT->name => 0,
        ];

        $houseWins = [
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

        $houseLosses = [
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

        $housePresence = [
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

        $setLosses = [
            KeyforgeSet::CotA->name => 0,
            KeyforgeSet::AoA->name => 0,
            KeyforgeSet::WC->name => 0,
            KeyforgeSet::MM->name => 0,
            KeyforgeSet::DT->name => 0,
        ];

        $setPresence = [
            KeyforgeSet::CotA->name => 0,
            KeyforgeSet::AoA->name => 0,
            KeyforgeSet::WC->name => 0,
            KeyforgeSet::MM->name => 0,
            KeyforgeSet::DT->name => 0,
        ];

        return [$setWins, $houseWins, $houseLosses, $housePresence, $setLosses, $setPresence];
    }
}
