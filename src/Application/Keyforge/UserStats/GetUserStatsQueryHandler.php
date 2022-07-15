<?php declare(strict_types=1);

namespace AdnanMula\Cards\Application\Keyforge\UserStats;

use AdnanMula\Cards\Domain\Model\Keyforge\KeyforgeDeck;
use AdnanMula\Cards\Domain\Model\Keyforge\KeyforgeRepository;
use AdnanMula\Cards\Domain\Model\User\UserRepository;

final class GetUserStatsQueryHandler
{
    public function __construct(
        private KeyforgeRepository $repository,
        private UserRepository $userRepository
    ) {}

    /** @return array<KeyforgeDeck> */
    public function __invoke(GetUserStatsQuery $query): array
    {
        $games = $this->repository->gamesByUser($query->userId());

        $userIds = [];
        $decksIds = [];

        foreach ($games as $game) {
            $userIds[] = $game->winner();
            $userIds[] = $game->loser();
            $decksIds[] = $game->winnerDeck();
            $decksIds[] = $game->loserDeck();
        }

        $decks = $this->repository->byIds(...$decksIds);
        $users = $this->userRepository->byIds(...$userIds);

        $indexedDecks = [];
        foreach ($decks as $deck) {
            $indexedDecks[$deck->id()->value()] = $deck->name();
        }

        $indexedUsers = [];
        foreach ($users as $user) {
            $indexedUsers[$user->id()->value()] = $user->name();
        }

        $result = [
            'games' => [],
        ];

        foreach ($games as $game) {
            $result['games'][] = [
                'winner' => $game->winner()->value(),
                'winner_name' => $indexedUsers[$game->winner()->value()],
                'winner_deck' => $game->winnerDeck()->value(),
                'winner_deck_name' => $indexedDecks[$game->winnerDeck()->value()],
                'loser' => $game->loser()->value(),
                'loser_name' => $indexedUsers[$game->loser()->value()],
                'loser_deck' => $game->loserDeck()->value(),
                'loser_deck_name' => $indexedDecks[$game->loserDeck()->value()],
                'score' => $game->score()->winnerScore() . '/' . $game->score()->loserScore(),
                'first_turn' => null === $game->firstTurn() ? null : $indexedUsers[$game->firstTurn()->value()],
                'date' => $game->date()->format('Y-m-d'),
            ];
        }

        $winRateVsUser = [];
        foreach ($users as $user) {
            if ($user->id()->equalTo($query->userId())) {
                continue;
            }

            $winRateVsUser[$user->id()->value()] = ['wins' => 0, 'loses' => 0];
        }


        $winsByDate = [];
        $losesByDate = [];

        foreach ($games as $game) {
            $isWin = $game->winner()->equalTo($query->userId());

            if ($isWin) {
                $winRateVsUser[$game->loser()->value()] = [
                    'wins' => $winRateVsUser[$game->loser()->value()]['wins'] + 1,
                    'loses' => $winRateVsUser[$game->loser()->value()]['loses'],
                ];
//
                $winsByDate[$game->date()->format('Y-m-d')] = isset($winsByDate[$game->date()->format('Y-m-d')])
                    ? $winsByDate[$game->date()->format('Y-m-d')] + 1
                    : 1;
            } else {
                $winRateVsUser[$game->winner()->value()] = [
                    'wins' => $winRateVsUser[$game->winner()->value()]['wins'],
                    'loses' => $winRateVsUser[$game->winner()->value()]['loses'] + 1,
                ];

                $losesByDate[$game->date()->format('Y-m-d')] = isset($losesByDate[$game->date()->format('Y-m-d')])
                    ? $losesByDate[$game->date()->format('Y-m-d')] + 1
                    : 1;
            }
        }

        $resultWinRateByUser = [];
        $resultPickRateByUser = [];

        foreach ($winRateVsUser as $userId => $winRate) {
            $name = $indexedUsers[$userId];

            $resultWinRateByUser[$name] = $this->winRate($winRate['wins'], $winRate['loses']);
            $resultPickRateByUser[$name] = $this->pickRate($winRate['wins'] + $winRate['loses'], \count($games));
        }

        $result['win_rate_vs_users'] = $resultWinRateByUser;
        $result['pick_rate_vs_users'] = $resultPickRateByUser;


        $dates = \array_values(\array_unique(\array_merge(\array_keys($winsByDate), \array_keys($losesByDate))));

        usort($dates, function (string $a, string $b) {
           return new \DateTimeImmutable($a) <=> new \DateTimeImmutable($b);
        });

        $resultDates = [];
        foreach ($dates as $date) {
            $resultDates[$date] = 0;
        }

        $resultWinsByDate = $resultDates;
        $resultLosesByDate = $resultDates;

        foreach ($winsByDate as $date => $winByDate) {
            $resultWinsByDate[$date] += $winByDate;
        }

        foreach ($losesByDate as $date => $lossByDate) {
            $resultLosesByDate[$date] += $lossByDate;
        }

        $result['wins_by_date'] = $resultWinsByDate;
        $result['loses_by_date'] = $resultLosesByDate;

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

    private function pickRate(int $picks, int $games): float
    {
        if ($games === 0) {
            return 0;
        }

        return \round($picks / $games * 100, 2);
    }
}
