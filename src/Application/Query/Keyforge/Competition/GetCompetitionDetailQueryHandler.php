<?php declare(strict_types=1);

namespace AdnanMula\Cards\Application\Query\Keyforge\Competition;

use AdnanMula\Cards\Domain\Model\Keyforge\Game\KeyforgeCompetitionRepository;
use AdnanMula\Cards\Domain\Model\Keyforge\Game\KeyforgeGameRepository;
use AdnanMula\Cards\Domain\Model\Keyforge\User\KeyforgeUserRepository;
use AdnanMula\Criteria\Criteria;
use AdnanMula\Criteria\Filter\Filter;
use AdnanMula\Criteria\Filter\FilterType;
use AdnanMula\Criteria\FilterField\FilterField;
use AdnanMula\Criteria\FilterGroup\AndFilterGroup;
use AdnanMula\Criteria\FilterValue\FilterOperator;
use AdnanMula\Criteria\FilterValue\StringArrayFilterValue;

final readonly class GetCompetitionDetailQueryHandler
{
    public function __construct(
        private KeyforgeCompetitionRepository $repository,
        private KeyforgeUserRepository $userRepository,
        private KeyforgeGameRepository $gameRepository,
    ) {}

    public function __invoke(GetCompetitionDetailQuery $query): array
    {
        $users = $this->userRepository->search(new Criteria(null, null, null));

        $indexedUsers = [];
        foreach ($users as $user) {
            $indexedUsers[$user->id()->value()] = $user;
        }

        $competition = $this->repository->byReference($query->reference);

        if (null === $competition) {
            throw new \Exception('Competition not found');
        }

        $fixtures = $this->repository->fixtures($competition->id());

        $gameIds = [];

        foreach ($fixtures as $fixture) {
            foreach ($fixture->games() as $gameId) {
                $gameIds[] = $gameId->value();
            }
        }

        $games = $this->gameRepository->search(new Criteria(
            null,
            null,
            null,
            new AndFilterGroup(
                FilterType::AND,
                new Filter(new FilterField('id'), new StringArrayFilterValue(...$gameIds), FilterOperator::IN),
            ),
        ));

        $indexedGames = [];
        foreach ($games as $game) {
            $indexedGames[$game->id()->value()] = $game;
        }

        $indexedFixtures = [];
        $fixturesIsMatch = false;

        foreach ($fixtures as $fixture) {
            $fixturesIsMatch = $fixture->type()->isBestOf();

            $users = [];

            foreach ($fixture->users() as $user) {
                $users[] = [
                    'id' => $user->value(),
                    'name' => $indexedUsers[$user->value()]->name(),
                ];
            }

            $fixtureArray = $fixture->jsonSerialize();
            $fixtureArray['users'] = $users;
            $fixtureArray['games'] = [];
            if (\count($fixture->games()) > 0) {
                foreach ($fixture->games() as $gameId) {
                    $fixtureArray['games'][] = $indexedGames[$gameId->value()]->jsonSerialize();
                }
            }

            $indexedFixtures[$fixture->reference()][] = $fixtureArray;
        }

        $classification = [];

        foreach ($competition->users() as $user) {
            $player = [
                'position' => 0,
                'user' => $user->value(),
                'username' => $indexedUsers[$user->value()]->name(),
                'wins' => 0,
                'losses' => 0,
                'keys_forged' => 0,
                'keys_opponent_forged' => 0,
                'game_wins' => 0,
                'game_losses' => 0,
            ];

            foreach ($indexedFixtures as $fixtures) {
                foreach ($fixtures as $fixture) {
                    if (null === $fixture['winner']) {
                        continue;
                    }

                    if (false === \in_array($user->value(), \array_map(static fn (array $user): string => $user['id'], $fixture['users']), true)) {
                        continue;
                    }

                    if (\count($fixture['games']) === 0) {
                        continue;
                    }

                    if ($user->value() === $fixture['winner']) {
                        $player['wins']++;
                    } else {
                        $player['losses']++;
                    }

                    foreach ($fixture['games'] as $game) {
                        if ($user->value() === $game['winner']) {
                            $player['game_wins']++;
                            $player['keys_forged'] += $game['score']['winner_score'];
                            $player['keys_opponent_forged'] += $game['score']['loser_score'];
                        }

                        if ($user->value() === $game['loser']) {
                            $player['game_losses']++;
                            $player['keys_forged'] += $game['score']['loser_score'];
                            $player['keys_opponent_forged'] += $game['score']['winner_score'];
                        }
                    }
                }
            }

            $classification[] = $player;
        }

        if (false === $fixturesIsMatch) {
            foreach ($classification as &$position) {
                $position['wins'] = $position['game_wins'];
                $position['losses'] = $position['game_losses'];
            }
        }

        \usort($classification, static function (array $a, array $b) {
            if ($b['wins'] === $a['wins'] && $a['losses'] === $b['losses']) {
                $aDiff = $a['keys_forged'] - $a['keys_opponent_forged'];
                $bDiff = $b['keys_forged'] - $b['keys_opponent_forged'];

                if ($aDiff === $bDiff) {
                    if ($a['keys_forged'] === $b['keys_forged']) {
                        return $a['keys_opponent_forged'] <=> $b['keys_opponent_forged'];
                    }

                    return $b['keys_forged'] <=> $a['keys_forged'];
                }

                return $bDiff <=> $aDiff;
            }

            if ($b['wins'] === $a['wins']) {
                return $a['losses'] <=> $b['losses'];
            }

            return $b['wins'] <=> $a['wins'];
        });

        foreach ($classification as $position => &$player) {
            $player['position'] = $position + 1;
        }

        $indexedFixtures = $this->order($indexedFixtures);

        return [
            'competition' => $competition->jsonSerialize(),
            'fixtures' => $indexedFixtures,
            'classification' => $classification,
        ];
    }

    private function order(array $indexedFixtures): array
    {
        foreach ($indexedFixtures as &$fixturesToSort) {
            \usort($fixturesToSort, static function (array $a, array $b) {
                return $a['position'] <=> $b['position'];
            });
        }

        $keys = \array_keys($indexedFixtures);

        \array_multisort(
            $keys,
            \SORT_NATURAL,
            $indexedFixtures,
        );

        return $indexedFixtures;
    }
}
