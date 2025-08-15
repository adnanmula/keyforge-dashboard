<?php declare(strict_types=1);

namespace AdnanMula\Cards\Application\Query\Keyforge\Competition;

use AdnanMula\Cards\Domain\Model\Keyforge\Game\KeyforgeCompetition;
use AdnanMula\Cards\Domain\Model\Keyforge\Game\KeyforgeCompetitionRepository;
use AdnanMula\Cards\Domain\Model\Keyforge\Game\KeyforgeGame;
use AdnanMula\Cards\Domain\Model\Keyforge\Game\KeyforgeGameRepository;
use AdnanMula\Cards\Domain\Model\Shared\ValueObject\Uuid;
use AdnanMula\Criteria\Criteria;
use AdnanMula\Criteria\Filter\Filter;
use AdnanMula\Criteria\Filter\FilterOperator;
use AdnanMula\Criteria\Filter\Filters;
use AdnanMula\Criteria\Filter\FilterType;
use AdnanMula\Criteria\FilterField\FilterField;
use AdnanMula\Criteria\FilterValue\StringArrayFilterValue;
use AdnanMula\Criteria\FilterValue\StringFilterValue;
use AdnanMula\Tournament\Classification\Player;

final readonly class GetCompetitionDetailQueryHandler
{
    public function __construct(
        private KeyforgeCompetitionRepository $repository,
        private KeyforgeGameRepository $gameRepository,
    ) {}

    public function __invoke(GetCompetitionDetailQuery $query): KeyforgeCompetition
    {
        $competition = $this->competition($query->id);
        $fixtures = $this->repository->fixtures($competition->id);

        foreach ($competition->players as $index => $player) {
            $competition->classification->addPlayer(new Player(
                $index + 1,
                $player,
            ));
        }

        $games = [];

        if (null !== $competition->startedAt) {
            $gameIds = [];

            foreach ($fixtures as $fixture) {
                $competition->fixtures->add($fixture);
                $gameIds = array_merge($gameIds, $fixture->games);
            }

            if (count($gameIds) > 0) {
                $games = $this->games(...$gameIds);
            }


            foreach ($fixtures as $fixture) {
                if (null === $fixture->winner) {
                    continue;
                }

                $winner = $competition->classification->playerWithId($fixture->winner->value());
                $winner->addWin();
                $loser = null;

                foreach ($fixture->games as $gameId) {
                    $game = $games[$gameId->value()];

                    if (null === $game) {
                        continue;
                    }

                    $winner->addGameWin();
                    $winner->addPointsPositive(3);
                    $winner->addPointsNegative($game->score()->loserScore());

                    $loser = $competition->classification->playerWithId($game->loser()->value());

                    if (null !== $loser) {
                        $loser->addGameLoss();
                        $loser->addPointsPositive($game->score()->loserScore());
                        $loser->addPointsNegative(3);
                    }
                }

                $loser?->addLose();
            }
        }

        $competition->classification->order();

        return $competition;
    }

    private function competition(Uuid $id): KeyforgeCompetition
    {
        $competition = $this->repository->searchOne(
            new Criteria(
                new Filters(
                    FilterType::AND,
                    new Filter(
                        new FilterField('id'),
                        new StringFilterValue($id->value()),
                        FilterOperator::EQUAL,
                    ),
                ),
            ),
        );

        if (null === $competition) {
            throw new \Exception('Competition not found');
        }

        return $competition;
    }

    /** @return array<string, KeyforgeGame> */
    private function games(Uuid ...$ids): array
    {
        $games = $this->gameRepository->search(new Criteria(
            new Filters(
                FilterType::AND,
                new Filter(
                    new FilterField('id'),
                    new StringArrayFilterValue(...\array_map(static fn ($id) => $id->value(), $ids)),
                    FilterOperator::IN,
                ),
            ),
        ));

        $indexedGames = [];

        foreach ($games as $game) {
            $indexedGames[$game->id()->value()] = $game;
        }

        return $indexedGames;
    }
}
