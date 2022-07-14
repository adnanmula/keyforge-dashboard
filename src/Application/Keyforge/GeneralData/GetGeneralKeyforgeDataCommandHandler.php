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

        $setLoses = [
            KeyforgeSet::CotA->name => 0,
            KeyforgeSet::AoA->name => 0,
            KeyforgeSet::WC->name => 0,
            KeyforgeSet::MM->name => 0,
            KeyforgeSet::DT->name => 0,
        ];

        $houseLoses = [
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

            $setWins[$winnerDeck->set()->name]++;

            foreach ($winnerDeck->houses()->value() as $house) {
                $houseWins[$house->name]++;
            }

            $loserDeck = $indexedDecks[$game->loserDeck()->value()];

            $setLoses[$loserDeck->set()->name]++;

            foreach ($loserDeck->houses()->value() as $house) {
                $houseLoses[$house->name]++;
            }
        }

        $setWinRate = [
            KeyforgeSet::CotA->name => $this->winRate($setWins[KeyforgeSet::CotA->name], $setLoses[KeyforgeSet::CotA->name]),
            KeyforgeSet::AoA->name => $this->winRate($setWins[KeyforgeSet::AoA->name], $setLoses[KeyforgeSet::AoA->name]),
            KeyforgeSet::WC->name => $this->winRate($setWins[KeyforgeSet::WC->name], $setLoses[KeyforgeSet::WC->name]),
            KeyforgeSet::MM->name => $this->winRate($setWins[KeyforgeSet::MM->name], $setLoses[KeyforgeSet::MM->name]),
            KeyforgeSet::DT->name => $this->winRate($setWins[KeyforgeSet::DT->name], $setLoses[KeyforgeSet::DT->name]),
        ];

        $houseWinRate = [
            KeyforgeHouse::SANCTUM->name => $this->winRate($houseWins[KeyforgeHouse::SANCTUM->name], $houseLoses[KeyforgeHouse::SANCTUM->name]),
            KeyforgeHouse::DIS->name => $this->winRate($houseWins[KeyforgeHouse::DIS->name], $houseLoses[KeyforgeHouse::DIS->name]),
            KeyforgeHouse::MARS->name => $this->winRate($houseWins[KeyforgeHouse::MARS->name], $houseLoses[KeyforgeHouse::MARS->name]),
            KeyforgeHouse::STAR_ALLIANCE->name => $this->winRate($houseWins[KeyforgeHouse::STAR_ALLIANCE->name], $houseLoses[KeyforgeHouse::STAR_ALLIANCE->name]),
            KeyforgeHouse::SAURIAN->name => $this->winRate($houseWins[KeyforgeHouse::SAURIAN->name], $houseLoses[KeyforgeHouse::SAURIAN->name]),
            KeyforgeHouse::SHADOWS->name => $this->winRate($houseWins[KeyforgeHouse::SHADOWS->name], $houseLoses[KeyforgeHouse::SHADOWS->name]),
            KeyforgeHouse::UNTAMED->name => $this->winRate($houseWins[KeyforgeHouse::UNTAMED->name], $houseLoses[KeyforgeHouse::UNTAMED->name]),
            KeyforgeHouse::BROBNAR->name => $this->winRate($houseWins[KeyforgeHouse::BROBNAR->name], $houseLoses[KeyforgeHouse::BROBNAR->name]),
            KeyforgeHouse::UNFATHOMABLE->name => $this->winRate($houseWins[KeyforgeHouse::UNFATHOMABLE->name], $houseLoses[KeyforgeHouse::UNFATHOMABLE->name]),
            KeyforgeHouse::LOGOS->name => $this->winRate($houseWins[KeyforgeHouse::LOGOS->name], $houseLoses[KeyforgeHouse::LOGOS->name]),
        ];

        $result = [
            'deck_count' => \count($decks),
            'set_presence' => $setPresence,
            'house_presence' => $housePresence,
            'games_played' => \count($games),
            'deck_win_rate' => $maxWinRateResult,
            'deck_max_sas' => $maxSasResult,
            'deck_min_sas' => $minSasResult,
            'set_wins' => $setWins,
            'house_wins' => $houseWins,
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
