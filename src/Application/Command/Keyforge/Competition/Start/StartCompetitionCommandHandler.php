<?php declare(strict_types=1);

namespace AdnanMula\Cards\Application\Command\Keyforge\Competition\Start;

use AdnanMula\Cards\Domain\Model\Keyforge\Game\KeyforgeCompetitionRepository;
use AdnanMula\Criteria\Criteria;
use AdnanMula\Criteria\Filter\Filter;
use AdnanMula\Criteria\Filter\FilterType;
use AdnanMula\Criteria\FilterField\FilterField;
use AdnanMula\Criteria\FilterGroup\AndFilterGroup;
use AdnanMula\Criteria\FilterValue\FilterOperator;
use AdnanMula\Criteria\FilterValue\StringFilterValue;

final readonly class StartCompetitionCommandHandler
{
    public function __construct(
        private KeyforgeCompetitionRepository $repository,
    ) {}

    public function __invoke(StartCompetitionCommand $command): void
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
                        new StringFilterValue($command->competitionId->value()),
                        FilterOperator::EQUAL,
                    ),
                ),
            ),
        );

        if (null === $competition) {
            throw new \Exception('Competition not found');
        }

        if (null !== $competition->startedAt()) {
            throw new \Exception('Competition already started');
        }

        if (null !== $competition->finishedAt()) {
            throw new \Exception('Competition already finished');
        }

        $competition->updateStartDate($command->date);

        $this->repository->save($competition);
    }
}
