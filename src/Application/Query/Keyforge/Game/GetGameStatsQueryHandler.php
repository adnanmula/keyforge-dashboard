<?php declare(strict_types=1);

namespace AdnanMula\Cards\Application\Query\Keyforge\Game;

use AdnanMula\Cards\Domain\Model\Keyforge\Deck\KeyforgeDeckRepository;
use AdnanMula\Cards\Domain\Model\Keyforge\Deck\ValueObject\KeyforgeSet;
use AdnanMula\Cards\Domain\Model\Keyforge\Game\KeyforgeGameRepository;
use AdnanMula\Criteria\Criteria;
use AdnanMula\Criteria\Filter\Filter;
use AdnanMula\Criteria\Filter\FilterOperator;
use AdnanMula\Criteria\Filter\Filters;
use AdnanMula\Criteria\Filter\FilterType;
use AdnanMula\Criteria\FilterField\FilterField;
use AdnanMula\Criteria\FilterValue\StringArrayFilterValue;

final readonly class GetGameStatsQueryHandler
{
    public function __construct(
        private KeyforgeGameRepository $gameRepository,
        private KeyforgeDeckRepository $deckRepository,
    ) {}

    public function __invoke(GetGameStatsQuery $query): array
    {
        $gamesQuery = new GetGamesQuery(
            deckId: $query->deckId,
            userId: $query->userId,
            winners: $query->winners,
            losers: $query->losers,
            loserScores: $query->loserScores,
            competitions: $query->competitions,
            approved: true,
            dateFrom: $query->dateFrom,
            dateTo: $query->dateTo,
            logStats: $query->logStats ?: null,
            orderField: 'date',
            orderDirection: 'asc',
        );

        $games = $this->gameRepository->search($gamesQuery->criteria);

        $deckIds = [];
        foreach ($games as $game) {
            $deckIds[] = $game->winnerDeck()->value();
            $deckIds[] = $game->loserDeck()->value();
        }
        $deckIds = \array_values(\array_unique(\array_filter($deckIds)));

        $deckDataById = [];
        if (\count($deckIds) > 0) {
            $decks = $this->deckRepository->search(new Criteria(
                new Filters(
                    FilterType::AND,
                    new Filter(
                        new FilterField('id'),
                        new StringArrayFilterValue(...$deckIds),
                        FilterOperator::IN,
                    ),
                ),
            ));

            foreach ($decks as $deck) {
                $deckDataById[$deck->id()->value()] = [
                    'set' => $deck->set()->value,
                    'sas' => $deck->stats()->sas,
                ];
            }
        }

        return [
            'winrate_over_time' => $this->computeWinrateOverTime($games, $query->userId, $query->deckId),
            'avg_stats' => $this->computeAvgStats($games),
            'player_avg_stats' => $this->computePlayerAvgStats($games, $query->userId, $query->deckId),
            'win_streak' => $this->computeWinStreak($games, $query->userId, $query->deckId),
            'winrate_by_set' => $this->computeWinrateBySet($games, $query->userId, $query->deckId, $deckDataById),
            'winrate_by_sas' => $this->computeWinrateBySas($games, $query->userId, $query->deckId, $deckDataById),
        ];
    }

    private function isWin(mixed $game, ?string $userId, ?string $deckId): bool
    {
        if (null !== $userId) {
            return $game->winner()->value() === $userId;
        }

        if (null !== $deckId) {
            return $game->winnerDeck()->value() === $deckId;
        }

        return false;
    }

    private function computeWinrateOverTime(array $games, ?string $userId, ?string $deckId = null): array
    {
        $byMonth = [];

        foreach ($games as $game) {
            $month = \substr($game->date()->format('Y-m-d'), 0, 7);

            if (!isset($byMonth[$month])) {
                $byMonth[$month] = ['wins' => 0, 'losses' => 0];
            }

            if ($this->isWin($game, $userId, $deckId)) {
                ++$byMonth[$month]['wins'];
            } else {
                ++$byMonth[$month]['losses'];
            }
        }

        \ksort($byMonth);

        $result = [];
        $cumWins = 0;
        $cumTotal = 0;

        foreach ($byMonth as $month => $counts) {
            $cumWins += $counts['wins'];
            $cumTotal += $counts['wins'] + $counts['losses'];
            $total = $counts['wins'] + $counts['losses'];
            $result[] = [
                'month' => $month,
                'wins' => $counts['wins'],
                'losses' => $counts['losses'],
                'total' => $total,
                'winrate' => $total > 0 ? \round($counts['wins'] / $total * 100, 1) : 0,
                'cum_winrate' => $cumTotal > 0 ? \round($cumWins / $cumTotal * 100, 1) : 0,
            ];
        }

        return $result;
    }

    private function computeAvgStats(array $games): array
    {
        $statKeys = [
            'turns',
            'winner_amber_obtained', 'winner_amber_stolen',
            'winner_cards_played', 'winner_cards_drawn', 'winner_cards_discarded',
            'winner_keys_forged', 'winner_fights', 'winner_reaps', 'winner_extra_turns',
            'loser_amber_obtained', 'loser_amber_stolen',
            'loser_cards_played', 'loser_cards_drawn', 'loser_cards_discarded',
            'loser_keys_forged', 'loser_fights', 'loser_reaps', 'loser_extra_turns',
        ];

        $sums = \array_fill_keys($statKeys, 0);
        $counts = \array_fill_keys($statKeys, 0);

        foreach ($games as $game) {
            $stats = $game->logStats();
            if (null === $stats) {
                continue;
            }

            foreach ($statKeys as $key) {
                if (isset($stats[$key])) {
                    $sums[$key] += $stats[$key];
                    $counts[$key] += 1;
                }
            }
        }

        $avgs = [];
        foreach ($statKeys as $key) {
            $avgs[$key] = $counts[$key] > 0 ? \round($sums[$key] / $counts[$key], 1) : null;
        }

        return $avgs;
    }

    private function computePlayerAvgStats(array $games, ?string $userId, ?string $deckId = null): array
    {
        if (null === $userId && null === $deckId) {
            return [];
        }

        $perGameKeys = [
            'amber_obtained', 'amber_stolen',
            'cards_played', 'cards_drawn', 'cards_discarded',
            'keys_forged', 'fights', 'reaps', 'extra_turns',
        ];

        $sums = ['turns' => 0];
        $counts = ['turns' => 0];
        foreach ($perGameKeys as $key) {
            $sums[$key] = 0;
            $counts[$key] = 0;
        }

        foreach ($games as $game) {
            $stats = $game->logStats();
            if (null === $stats) {
                continue;
            }

            if (isset($stats['turns'])) {
                $sums['turns'] += $stats['turns'];
                $counts['turns'] += 1;
            }

            $side = $this->isWin($game, $userId, $deckId) ? 'winner' : 'loser';

            foreach ($perGameKeys as $key) {
                $srcKey = $side . '_' . $key;
                if (isset($stats[$srcKey])) {
                    $sums[$key] += $stats[$srcKey];
                    $counts[$key] += 1;
                }
            }
        }

        $avgs = [];
        $avgs['turns'] = $counts['turns'] > 0 ? \round($sums['turns'] / $counts['turns'], 1) : null;
        foreach ($perGameKeys as $key) {
            $avgs[$key] = $counts[$key] > 0 ? \round($sums[$key] / $counts[$key], 1) : null;
        }

        return $avgs;
    }

    private function computeWinStreak(array $games, ?string $userId, ?string $deckId = null): array
    {
        $currentWinStreak = 0;
        $currentLossStreak = 0;
        $bestWinStreak = 0;
        $bestLossStreak = 0;
        $lastResult = null;
        $runStreak = 0;

        foreach ($games as $game) {
            $isWin = $this->isWin($game, $userId, $deckId);

            if ($lastResult === null) {
                $lastResult = $isWin;
                $runStreak = 1;
            } elseif ($lastResult === $isWin) {
                ++$runStreak;
            } else {
                if ($lastResult) {
                    $bestWinStreak = \max($bestWinStreak, $runStreak);
                } else {
                    $bestLossStreak = \max($bestLossStreak, $runStreak);
                }
                $lastResult = $isWin;
                $runStreak = 1;
            }
        }

        if ($lastResult !== null) {
            if ($lastResult) {
                $currentWinStreak = $runStreak;
                $currentLossStreak = 0;
                $bestWinStreak = \max($bestWinStreak, $runStreak);
            } else {
                $currentLossStreak = $runStreak;
                $currentWinStreak = 0;
                $bestLossStreak = \max($bestLossStreak, $runStreak);
            }
        }

        return [
            'current_win' => $currentWinStreak,
            'current_loss' => $currentLossStreak,
            'best_win' => $bestWinStreak,
            'best_loss' => $bestLossStreak,
        ];
    }

    private function computeWinrateBySet(array $games, ?string $userId, ?string $deckId, array $deckDataById): array
    {
        $bySet = [];

        foreach ($games as $game) {
            $isWin = $this->isWin($game, $userId, $deckId);
            $lookupId = null !== $deckId && null === $userId
                ? ($isWin ? $game->loserDeck()->value() : $game->winnerDeck()->value())
                : ($isWin ? $game->winnerDeck()->value() : $game->loserDeck()->value());
            $set = $deckDataById[$lookupId]['set'] ?? 'Unknown';

            if (!isset($bySet[$set])) {
                $bySet[$set] = ['wins' => 0, 'losses' => 0];
            }

            if ($isWin) {
                ++$bySet[$set]['wins'];
            } else {
                ++$bySet[$set]['losses'];
            }
        }

        \ksort($bySet);

        $setOrder = \array_values(\array_map(
            static fn (KeyforgeSet $s) => $s->value,
            KeyforgeSet::cases(),
        ));

        \uksort($bySet, static function (string $a, string $b) use ($setOrder): int {
            $posA = \array_search($a, $setOrder, true);
            $posB = \array_search($b, $setOrder, true);
            $posA = $posA === false ? \PHP_INT_MAX : $posA;
            $posB = $posB === false ? \PHP_INT_MAX : $posB;

            return $posA <=> $posB;
        });

        $result = [];
        foreach ($bySet as $set => $counts) {
            $total = $counts['wins'] + $counts['losses'];
            $result[] = [
                'set' => $set,
                'wins' => $counts['wins'],
                'losses' => $counts['losses'],
                'total' => $total,
                'winrate' => $total > 0 ? \round($counts['wins'] / $total * 100, 1) : 0,
            ];
        }

        return $result;
    }

    private function computeWinrateBySas(array $games, ?string $userId, ?string $deckId, array $deckDataById): array
    {
        $bySas = [];

        foreach ($games as $game) {
            $isWin = $this->isWin($game, $userId, $deckId);
            $lookupId = null !== $deckId && null === $userId
                ? ($isWin ? $game->loserDeck()->value() : $game->winnerDeck()->value())
                : ($isWin ? $game->winnerDeck()->value() : $game->loserDeck()->value());
            $sas = $deckDataById[$lookupId]['sas'] ?? null;

            if (null === $sas || $sas < 30) {
                continue;
            }

            $bucket = (int) (\floor($sas / 5) * 5);
            $label = $bucket . '-' . ($bucket + 4);

            if (!isset($bySas[$label])) {
                $bySas[$label] = ['wins' => 0, 'losses' => 0, 'sort' => $bucket];
            }

            if ($isWin) {
                ++$bySas[$label]['wins'];
            } else {
                ++$bySas[$label]['losses'];
            }
        }

        \uasort($bySas, static fn ($a, $b) => $a['sort'] <=> $b['sort']);

        $result = [];
        foreach ($bySas as $label => $counts) {
            $total = $counts['wins'] + $counts['losses'];
            $result[] = [
                'sas' => $label,
                'wins' => $counts['wins'],
                'losses' => $counts['losses'],
                'total' => $total,
                'winrate' => $total > 0 ? \round($counts['wins'] / $total * 100, 1) : 0,
            ];
        }

        return $result;
    }
}
