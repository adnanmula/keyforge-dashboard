<?php declare(strict_types=1);

namespace AdnanMula\Cards\Application\Command\Keyforge\Competition\Create;

use AdnanMula\Cards\Domain\Model\Keyforge\Game\KeyforgeCompetition;
use AdnanMula\Cards\Domain\Model\Keyforge\Game\KeyforgeCompetitionRepository;
use AdnanMula\Cards\Domain\Model\Shared\ValueObject\Uuid;
use AdnanMula\Cards\Domain\Service\Keyforge\Competition\FixturesGeneratorService;
use AdnanMula\Criteria\Criteria;
use AdnanMula\Criteria\Filter\Filter;
use AdnanMula\Criteria\Filter\FilterType;
use AdnanMula\Criteria\FilterField\FilterField;
use AdnanMula\Criteria\FilterGroup\AndFilterGroup;
use AdnanMula\Criteria\FilterValue\FilterOperator;
use AdnanMula\Criteria\FilterValue\StringFilterValue;

final class CreateCompetitionCommandHandler
{
    public function __construct(
        private KeyforgeCompetitionRepository $repository,
        private FixturesGeneratorService $fixturesService,
    ) {}

    public function __invoke(CreateCompetitionCommand $command): void
    {
        $this->assertName($command);
        $this->assertPlayerCount($command);
        $this->assertType($command);

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

        $fixtures = [];

        if ($command->type->isRoundRobin()) {
            $fixtures = $this->fixturesService->execute($competition, $command->fixturesType);
        }

        $this->repository->save($competition);

        foreach ($fixtures as $fixture) {
            $this->repository->saveFixture($fixture);
        }
    }

    private function assertName(CreateCompetitionCommand $command): void
    {
        if (\in_array($command->reference, ['new', 'start', 'finish'], true)) {
            throw new \Exception('Nombre inválido.');
        }

        $withConflict = $this->repository->search(new Criteria(
            null,
            null,
            null,
            new AndFilterGroup(
                FilterType::OR,
                new Filter(new FilterField('name'), new StringFilterValue($command->name), FilterOperator::EQUAL),
                new Filter(new FilterField('reference'), new StringFilterValue($command->reference), FilterOperator::EQUAL),
            ),
        ));

        if (\count($withConflict) > 0) {
            throw new \Exception('Nombre ya en uso.');
        }
    }

    private function assertPlayerCount(CreateCompetitionCommand $command): void
    {
        if (\count($command->users) < 3) {
            throw new \Exception('No hay suficientes jugadores, mínimo tres son necesarios.');
        }
    }

    private function assertType(CreateCompetitionCommand $command): void
    {
        if (false === $command->type->isRoundRobin()) {
            throw new \Exception('Type not supported yet, only Round Robin (1/2) are available');
        }
    }
}
