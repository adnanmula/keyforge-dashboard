<?php declare(strict_types=1);

namespace AdnanMula\Cards\Application\Command\Keyforge\Stat\General;

use AdnanMula\Cards\Domain\Model\Keyforge\Deck\KeyforgeDeckRepository;
use AdnanMula\Cards\Domain\Model\Keyforge\Deck\ValueObject\KeyforgeHouse;
use AdnanMula\Cards\Domain\Model\Keyforge\Deck\ValueObject\KeyforgeSet;
use AdnanMula\Cards\Domain\Model\Keyforge\Game\KeyforgeCompetitionRepository;
use AdnanMula\Cards\Domain\Model\Keyforge\Game\KeyforgeGameRepository;
use AdnanMula\Cards\Domain\Model\Keyforge\Stat\KeyforgeStat;
use AdnanMula\Cards\Domain\Model\Keyforge\Stat\KeyforgeStatRepository;
use AdnanMula\Cards\Domain\Model\Keyforge\Stat\ValueObject\KeyforgeStatCategory;
use AdnanMula\Cards\Domain\Model\Shared\ValueObject\Uuid;
use AdnanMula\Criteria\Criteria;
use AdnanMula\Criteria\Filter\Filter;
use AdnanMula\Criteria\Filter\FilterType;
use AdnanMula\Criteria\FilterField\FilterField;
use AdnanMula\Criteria\FilterGroup\AndFilterGroup;
use AdnanMula\Criteria\FilterValue\FilterOperator;
use AdnanMula\Criteria\FilterValue\NullFilterValue;

final class GenerateGeneralStatsCommandHandler
{
    public function __construct(
        private KeyforgeStatRepository $statRepository,
        private KeyforgeDeckRepository $deckRepository,
        private KeyforgeGameRepository $gameRepository,
        private KeyforgeCompetitionRepository $competitionRepository,
    ) {}

    public function __invoke(GenerateGeneralStatsCommand $command): void
    {
        $decks = $this->deckRepository->search(
            new Criteria(
                null,
                null,
                null,
                new AndFilterGroup(
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
            $setPresence[$deck->data()->set->name]++;

            foreach ($deck->data()->houses->value() as $house) {
                $housePresence[$house->name]++;
            }

            $winRate = $this->winRate($deck->userData()->wins, $deck->userData()->losses);

            if ($deck->data()->stats->sas > $maxSas) {
                $maxSas = $deck->data()->stats->sas;
                $maxSasResult = [
                    'id' => $deck->id()->value(),
                    'name' => $deck->data()->name,
                    'sas' => $deck->data()->stats->sas,
                    'wins' => $deck->userData()->wins,
                    'losses' => $deck->userData()->losses,
                    'win_rate' => $this->winRate($deck->userData()->wins, $deck->userData()->losses),
                ];
            }

            if ($deck->data()->stats->sas < $minSas) {
                $minSas = $deck->data()->stats->sas;
                $minSasResult = [
                    'id' => $deck->id()->value(),
                    'name' => $deck->data()->name,
                    'sas' => $deck->data()->stats->sas,
                    'wins' => $deck->userData()->wins,
                    'losses' => $deck->userData()->losses,
                    'win_rate' => $winRate,
                ];
            }

            if ($winRate > $maxWinRate) {
                $maxWinRate = $winRate;
                $maxWinRateResult = [
                    'id' => $deck->id()->value(),
                    'name' => $deck->data()->name,
                    'sas' => $deck->data()->stats->sas,
                    'wins' => $deck->userData()->wins,
                    'losses' => $deck->userData()->losses,
                    'win_rate' => $this->winRate($deck->userData()->wins, $deck->userData()->losses),
                ];
            }

            $indexedDecks[$deck->id()->value()] = $deck;
        }

        $games = $this->gameRepository->all();

        $gamesCount = 0;

        foreach ($games as $game) {
            if (false === \array_key_exists($game->winnerDeck()->value(), $indexedDecks)) {
                continue;
            }

            if (false === \array_key_exists($game->loserDeck()->value(), $indexedDecks)) {
                continue;
            }

            $winnerDeck = $indexedDecks[$game->winnerDeck()->value()];

            $setWins[$winnerDeck->data()->set->name]++;

            foreach ($winnerDeck->data()->houses->value() as $house) {
                $houseWins[$house->name]++;
            }

            $loserDeck = $indexedDecks[$game->loserDeck()->value()];

            $setLosses[$loserDeck->data()->set->name]++;

            foreach ($loserDeck->data()->houses->value() as $house) {
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
            KeyforgeSet::WoE->name => $this->winRate($setWins[KeyforgeSet::WoE->name], $setLosses[KeyforgeSet::WoE->name]),
            KeyforgeSet::GR->name => $this->winRate($setWins[KeyforgeSet::GR->name], $setLosses[KeyforgeSet::GR->name]),
            KeyforgeSet::AS->name => $this->winRate($setWins[KeyforgeSet::AS->name], $setLosses[KeyforgeSet::AS->name]),
            KeyforgeSet::U22->name => $this->winRate($setWins[KeyforgeSet::U22->name], $setLosses[KeyforgeSet::U22->name]),
            KeyforgeSet::M24->name => $this->winRate($setWins[KeyforgeSet::M24->name], $setLosses[KeyforgeSet::M24->name]),
            KeyforgeSet::VM23->name => $this->winRate($setWins[KeyforgeSet::VM23->name], $setLosses[KeyforgeSet::VM23->name]),
            KeyforgeSet::VM24->name => $this->winRate($setWins[KeyforgeSet::VM24->name], $setLosses[KeyforgeSet::VM24->name]),
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
            KeyforgeHouse::EKWIDON->name => $this->winRate($houseWins[KeyforgeHouse::EKWIDON->name], $houseLosses[KeyforgeHouse::EKWIDON->name]),
            KeyforgeHouse::GEISTOID->name => $this->winRate($houseWins[KeyforgeHouse::GEISTOID->name], $houseLosses[KeyforgeHouse::GEISTOID->name]),
            KeyforgeHouse::SKYBORN->name => $this->winRate($houseWins[KeyforgeHouse::SKYBORN->name], $houseLosses[KeyforgeHouse::SKYBORN->name]),
        ];

        $totalSetPicks = $gamesCount * 2;
        $totalHousePicks = $gamesCount * 6;

        $setPickRate = [
            KeyforgeSet::CotA->name => $this->pickRate($setWins[KeyforgeSet::CotA->name] + $setLosses[KeyforgeSet::CotA->name], $totalSetPicks),
            KeyforgeSet::AoA->name => $this->pickRate($setWins[KeyforgeSet::AoA->name] + $setLosses[KeyforgeSet::AoA->name], $totalSetPicks),
            KeyforgeSet::WC->name => $this->pickRate($setWins[KeyforgeSet::WC->name] + $setLosses[KeyforgeSet::WC->name], $totalSetPicks),
            KeyforgeSet::MM->name => $this->pickRate($setWins[KeyforgeSet::MM->name] + $setLosses[KeyforgeSet::MM->name], $totalSetPicks),
            KeyforgeSet::DT->name => $this->pickRate($setWins[KeyforgeSet::DT->name] + $setLosses[KeyforgeSet::DT->name], $totalSetPicks),
            KeyforgeSet::WoE->name => $this->pickRate($setWins[KeyforgeSet::WoE->name] + $setLosses[KeyforgeSet::WoE->name], $totalSetPicks),
            KeyforgeSet::GR->name => $this->pickRate($setWins[KeyforgeSet::GR->name] + $setLosses[KeyforgeSet::GR->name], $totalSetPicks),
            KeyforgeSet::AS->name => $this->pickRate($setWins[KeyforgeSet::AS->name] + $setLosses[KeyforgeSet::AS->name], $totalSetPicks),
            KeyforgeSet::U22->name => $this->pickRate($setWins[KeyforgeSet::U22->name] + $setLosses[KeyforgeSet::U22->name], $totalSetPicks),
            KeyforgeSet::M24->name => $this->pickRate($setWins[KeyforgeSet::M24->name] + $setLosses[KeyforgeSet::M24->name], $totalSetPicks),
            KeyforgeSet::VM23->name => $this->pickRate($setWins[KeyforgeSet::VM23->name] + $setLosses[KeyforgeSet::VM23->name], $totalSetPicks),
            KeyforgeSet::VM24->name => $this->pickRate($setWins[KeyforgeSet::VM24->name] + $setLosses[KeyforgeSet::VM24->name], $totalSetPicks),
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
            KeyforgeHouse::EKWIDON->name => $this->pickRate($houseWins[KeyforgeHouse::EKWIDON->name] + $houseLosses[KeyforgeHouse::EKWIDON->name], $totalHousePicks),
            KeyforgeHouse::GEISTOID->name => $this->pickRate($houseWins[KeyforgeHouse::GEISTOID->name] + $houseLosses[KeyforgeHouse::GEISTOID->name], $totalHousePicks),
            KeyforgeHouse::SKYBORN->name => $this->pickRate($houseWins[KeyforgeHouse::SKYBORN->name] + $houseLosses[KeyforgeHouse::SKYBORN->name], $totalHousePicks),
        ];

        $competitionCount = $this->competitionRepository->count(new Criteria(null, null, null));

        $this->statRepository->remove(KeyforgeStatCategory::HOME_GENERAL_DATA, null);

        $this->statRepository->save(new KeyforgeStat(
            Uuid::v4(),
            KeyforgeStatCategory::HOME_GENERAL_DATA,
            null,
            [
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
            ],
        ));
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
        $setArray = [
            KeyforgeSet::CotA->name => 0,
            KeyforgeSet::AoA->name => 0,
            KeyforgeSet::WC->name => 0,
            KeyforgeSet::MM->name => 0,
            KeyforgeSet::DT->name => 0,
            KeyforgeSet::WoE->name => 0,
            KeyforgeSet::GR->name => 0,
            KeyforgeSet::AS->name => 0,
            KeyforgeSet::U22->name => 0,
            KeyforgeSet::M24->name => 0,
            KeyforgeSet::VM23->name => 0,
            KeyforgeSet::VM24->name => 0,
        ];

        $houseArray = [
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
            KeyforgeHouse::GEISTOID->name => 0,
            KeyforgeHouse::SKYBORN->name => 0,
        ];

        return [$setArray, $houseArray, $houseArray, $houseArray, $setArray, $setArray];
    }
}