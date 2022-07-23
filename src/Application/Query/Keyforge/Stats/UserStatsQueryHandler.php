<?php declare(strict_types=1);

namespace AdnanMula\Cards\Application\Query\Keyforge\Stats;

use AdnanMula\Cards\Domain\Model\Keyforge\KeyforgeDeckRepository;
use AdnanMula\Cards\Domain\Model\Keyforge\KeyforgeGameRepository;
use AdnanMula\Cards\Domain\Model\Keyforge\KeyforgeUserRepository;
use AdnanMula\Cards\Domain\Model\Shared\Filter;
use AdnanMula\Cards\Domain\Model\Shared\SearchTerm;
use AdnanMula\Cards\Domain\Model\Shared\SearchTerms;
use AdnanMula\Cards\Domain\Model\Shared\SearchTermType;

final class UserStatsQueryHandler
{
    public function __construct(
        private KeyforgeDeckRepository $deckRepository,
        private KeyforgeGameRepository $gameRepository,
        private KeyforgeUserRepository $userRepository,
    ) {}

    public function __invoke(UserStatsQuery $query): array
    {
        $games = $this->gameRepository->search(new SearchTerms(
            new SearchTerm(
                SearchTermType::OR,
                new Filter('winner', $query->userId()->value()),
                new Filter('loser', $query->userId()->value()),
            ),
        ), null, null);

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

        $indexedDecks = [];

        foreach ($decks as $deck) {
            $indexedDecks[$deck->id()->value()] = $deck->name();
        }

        $bestAndWorseDecks = [];

        foreach ($indexedDecks as $id => $deck) {
            $bestAndWorseDecks[$id] = [
                'id' => $deck,
                'wins' => 0,
                'losses' => 0,
            ];
        }

        foreach ($games as $game) {
            if ($game->winner()->equalTo($query->userId())) {
                $bestAndWorseDecks[$game->winnerDeck()->value()] = [
                    'wins' => $bestAndWorseDecks[$game->winnerDeck()->value()]['wins'] + 1,
                    'losses' => $bestAndWorseDecks[$game->winnerDeck()->value()]['losses'],
                ];
            }

            if ($game->loser()->equalTo($query->userId())) {
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

        foreach ($winRateVsUser as $userId => $winRate) {
            $name = $indexedUsers[$userId];

            $resultWinRateByUser[$name] = $this->winRate($winRate['wins'], $winRate['losses']);
            $resultPickRateByUser[$name] = $this->pickRate($winRate['wins'] + $winRate['losses'], \count($games));
        }

        $result['win_rate_vs_users'] = $resultWinRateByUser;
        $result['pick_rate_vs_users'] = $resultPickRateByUser;

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
        }

        $result['wins_by_date'] = $resultWinsByDate;
        $result['losses_by_date'] = $resultLossesByDate;
        $result['best_deck'] = null === $bestDeck['id'] ? null : $bestDeck;
        $result['worse_deck'] = null === $worseDeck['id'] ? null : $worseDeck;

        return $result;
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
}
