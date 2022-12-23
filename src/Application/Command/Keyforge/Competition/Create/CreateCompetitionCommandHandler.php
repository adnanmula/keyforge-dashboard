<?php declare(strict_types=1);

namespace AdnanMula\Cards\Application\Command\Keyforge\Competition\Create;

use AdnanMula\Cards\Domain\Model\Keyforge\KeyforgeCompetition;
use AdnanMula\Cards\Domain\Model\Keyforge\KeyforgeCompetitionRepository;
use AdnanMula\Cards\Domain\Model\Shared\ValueObject\Uuid;
use AdnanMula\Cards\Infrastructure\Criteria\Criteria;
use AdnanMula\Cards\Infrastructure\Criteria\Filter\Filter;
use AdnanMula\Cards\Infrastructure\Criteria\Filter\Filters;
use AdnanMula\Cards\Infrastructure\Criteria\Filter\FilterType;
use AdnanMula\Cards\Infrastructure\Criteria\FilterField\FilterField;
use AdnanMula\Cards\Infrastructure\Criteria\FilterValue\FilterOperator;
use AdnanMula\Cards\Infrastructure\Criteria\FilterValue\StringFilterValue;

final class CreateCompetitionCommandHandler
{
    public function __construct(
        private KeyforgeCompetitionRepository $repository,
    ) {}

    public function __invoke(CreateCompetitionCommand $command): void
    {
        $this->assertName($command);

        $competition = new KeyforgeCompetition(
            Uuid::v4(),
            $command->reference,
            $command->name,
            $command->type,
            $command->users,
            $command->description,
            new \DateTimeImmutable(),
            null,
            null,
            null,
        );

        $this->repository->save($competition);
    }

    private function assertName(CreateCompetitionCommand $command): void
    {
        $withConflict = $this->repository->search(new Criteria(
            null,
            null,
            null,
            new Filters(
                FilterType::AND,
                FilterType::OR,
                new Filter(new FilterField('name'), new StringFilterValue($command->name), FilterOperator::EQUAL),
                new Filter(new FilterField('reference'), new StringFilterValue($command->reference), FilterOperator::EQUAL),
            ),
        ));

        if (\count($withConflict) > 0) {
            throw new \Exception('Name already in use.');
        }
    }
}
