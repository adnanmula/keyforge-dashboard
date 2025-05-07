<?php declare(strict_types=1);

namespace AdnanMula\Cards\Application\Query\Keyforge\Competition;

use AdnanMula\Cards\Domain\Model\Keyforge\Game\KeyforgeCompetition;
use AdnanMula\Cards\Domain\Model\Keyforge\Game\KeyforgeCompetitionRepository;
use AdnanMula\Cards\Domain\Model\Shared\ValueObject\Uuid;
use AdnanMula\Criteria\Criteria;
use AdnanMula\Criteria\Filter\Filter;
use AdnanMula\Criteria\Filter\FilterType;
use AdnanMula\Criteria\FilterField\FilterField;
use AdnanMula\Criteria\FilterGroup\AndFilterGroup;
use AdnanMula\Criteria\FilterValue\FilterOperator;
use AdnanMula\Criteria\FilterValue\StringFilterValue;
use AdnanMula\Tournament\Classification\Player;

final readonly class GetCompetitionDetailQueryHandler
{
    public function __construct(
        private KeyforgeCompetitionRepository $repository,
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

        if (null !== $competition->startedAt) {
            foreach ($fixtures as $fixture) {
                $competition->fixtures->add($fixture);
            }
        }

        return $competition;
    }

    private function competition(Uuid $id): KeyforgeCompetition
    {
        $competition = $this->repository->searchOne(
            new Criteria(
                null,
                null,
                null,
                new AndFilterGroup(
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
}
