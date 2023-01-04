<?php declare(strict_types=1);

namespace AdnanMula\Cards\Application\Query\Keyforge\Competition;

use AdnanMula\Cards\Domain\Model\Keyforge\KeyforgeCompetitionFixture;
use AdnanMula\Cards\Domain\Model\Keyforge\KeyforgeCompetitionRepository;
use AdnanMula\Cards\Domain\Model\Keyforge\KeyforgeGameRepository;
use AdnanMula\Cards\Domain\Model\Keyforge\KeyforgeUserRepository;
use AdnanMula\Cards\Infrastructure\Criteria\Criteria;
use AdnanMula\Cards\Infrastructure\Criteria\Filter\Filter;
use AdnanMula\Cards\Infrastructure\Criteria\Filter\Filters;
use AdnanMula\Cards\Infrastructure\Criteria\Filter\FilterType;
use AdnanMula\Cards\Infrastructure\Criteria\FilterField\FilterField;
use AdnanMula\Cards\Infrastructure\Criteria\FilterValue\FilterOperator;
use AdnanMula\Cards\Infrastructure\Criteria\FilterValue\StringArrayFilterValue;

final readonly class GetCompetitionDetailQueryHandler
{
    public function __construct(
        private KeyforgeCompetitionRepository $repository,
        private KeyforgeUserRepository $userRepository,
        private KeyforgeGameRepository $gameRepository,
    ) {}

    public function __invoke(GetCompetitionDetailQuery $query): array
    {
        $users = $this->userRepository->all(true);

        $indexedUsers = [];
        foreach ($users as $user) {
            $indexedUsers[$user->id()->value()] = $user;
        }

        $competition = $this->repository->byReference($query->reference);

        if (null === $competition) {
            throw new \Exception('Competition not found');
        }

        $fixtures = $this->repository->fixtures($competition->id());

        $gameIds = \array_filter(\array_map(static fn (KeyforgeCompetitionFixture $fixture): ?string => $fixture->game()?->value(), $fixtures));

        $games = $this->gameRepository->search(new Criteria(
            null,
            null,
            null,
            new Filters(
                FilterType::AND,
                FilterType::AND,
                new Filter(new FilterField('id'), new StringArrayFilterValue(...$gameIds), FilterOperator::IN),
            ),
        ));

        $indexedGames = [];
        foreach ($games as $game) {
            $indexedGames[$game->id()->value()] = $game;
        }

        $indexedFixtures = [];
        foreach ($fixtures as $fixture) {
            $users = [];

            foreach ($fixture->users() as $user) {
                $users[] = [
                    'id' => $user->value(),
                    'name' => $indexedUsers[$user->value()]->name(),
                ];
            }

            $fixtureArray = $fixture->jsonSerialize();
            $fixtureArray['users'] = $users;
            $fixtureArray['game'] = null;
            if (null !== $fixture->game()) {
                $fixtureArray['game'] = $indexedGames[$fixture->game()->value()]->jsonSerialize();
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
            ];

            foreach ($indexedFixtures as $fixtures) {
                foreach ($fixtures as $fixture) {
                    if (null === $fixture['game']) {
                        continue;
                    }

                    if ($user->value() === $fixture['game']['winner']) {
                        $player['wins']++;
                        $player['keys_forged'] += $fixture['game']['score']['winner_score'];
                    }

                    if ($user->value() === $fixture['game']['loser']) {
                        $player['losses']++;
                        $player['keys_opponent_forged'] += $fixture['game']['score']['loser_score'];
                    }
                }
            }

            $classification[] = $player;
        }


        \usort($classification, static function (array $a, array $b) {
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
