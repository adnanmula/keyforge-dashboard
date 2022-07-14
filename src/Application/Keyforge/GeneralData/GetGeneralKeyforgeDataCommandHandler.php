<?php declare(strict_types=1);

namespace AdnanMula\Cards\Application\Keyforge\GeneralData;

use AdnanMula\Cards\Domain\Model\Keyforge\KeyforgeDeck;
use AdnanMula\Cards\Domain\Model\Keyforge\KeyforgeRepository;
use AdnanMula\Cards\Domain\Model\Keyforge\ValueObject\KeyforgeHouse;
use AdnanMula\Cards\Domain\Model\Keyforge\ValueObject\KeyforgeSet;
use AdnanMula\Cards\Domain\Model\User\UserRepository;

final class GetGeneralKeyforgeDataCommandHandler
{
    public function __construct(
        private UserRepository $repository,
        private KeyforgeRepository $keyforgeRepository
    ) {}

    /** @return array<KeyforgeDeck> */
    public function __invoke(GetGeneralKeyforgeDataCommand $query): array
    {
        $decks = $this->keyforgeRepository->all(0, 2000);

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

        $setPresence = [
            KeyforgeSet::CotA->name => 0,
            KeyforgeSet::AoA->name => 0,
            KeyforgeSet::WC->name => 0,
            KeyforgeSet::MM->name => 0,
            KeyforgeSet::DT->name => 0,
        ];

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
                    'loses' => $deck->losses(),
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
                    'loses' => $deck->losses(),
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
                    'loses' => $deck->losses(),
                    'win_rate' => $this->winRate($deck->wins(), $deck->losses()),
                ];
            }

            $indexedDecks[$deck->id()->value()] = $deck;
        }

        $games = $this->keyforgeRepository->allGames(0, 10000);

        $setWinRate = [
            KeyforgeSet::CotA->name => 0,
            KeyforgeSet::AoA->name => 0,
            KeyforgeSet::WC->name => 0,
            KeyforgeSet::MM->name => 0,
            KeyforgeSet::DT->name => 0,
        ];

        $houseWinRate = [
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

        foreach ($games as $game) {
            if (false === \array_key_exists($game->winnerDeck()->value(), $indexedDecks)) {
                continue;
            }

            $winnerDeck = $indexedDecks[$game->winnerDeck()->value()];

            $setWinRate[$winnerDeck->set()->name]++;

            foreach ($winnerDeck->houses()->value() as $house) {
                $houseWinRate[$house->name]++;
            }
        }

        $result = [
            'deck_count' => \count($decks),
            'set_presence' => $setPresence,
            'house_presence' => $housePresence,
            'games_played' => \count($games),
            'deck_win_rate' => $maxWinRateResult,
            'deck_max_sas' => $maxSasResult,
            'deck_min_sas' => $minSasResult,
            'set_win_rate' => $setWinRate,
            'house_win_rate' => $houseWinRate,
        ];

        return $result;
    }

    private function winRate(int $wins, int $loses): float
    {
        $games = $wins + $loses;

        if ($games === 0) {
            return 0;
        }

        return round($wins/$games*100, 2);
    }
}
