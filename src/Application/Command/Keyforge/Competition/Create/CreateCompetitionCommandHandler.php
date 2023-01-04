<?php declare(strict_types=1);

namespace AdnanMula\Cards\Application\Command\Keyforge\Competition\Create;

use AdnanMula\Cards\Domain\Model\Keyforge\KeyforgeCompetition;
use AdnanMula\Cards\Domain\Model\Keyforge\KeyforgeCompetitionFixture;
use AdnanMula\Cards\Domain\Model\Keyforge\KeyforgeCompetitionRepository;
use AdnanMula\Cards\Domain\Model\Shared\ValueObject\CompetitionFixtureType;
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
            $fixtures = $this->generateFixtures($competition);
        }

        $this->repository->save($competition);

        foreach ($fixtures as $fixture) {
            $this->repository->saveFixture($fixture);
        }
    }

    private function assertName(CreateCompetitionCommand $command): void
    {
        if ('new' === $command->reference) {
            throw new \Exception('Invalid name.');
        }

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

    private function assertPlayerCount(CreateCompetitionCommand $command): void
    {
        if (\count($command->users) < 3) {
            throw new \Exception('Not enough players, minimum of three are required');
        }
    }

    private function assertType(CreateCompetitionCommand $command): void
    {
        if (false === $command->type->isRoundRobin()) {
            throw new \Exception('Type not supported yet, only Round Robin (1/2) are available');
        }
    }

    /** @return array<KeyforgeCompetitionFixture> */
    private function generateFixtures(KeyforgeCompetition $competition): array
    {
        $users = \array_map(static fn (Uuid $id): string => $id->value(), $competition->users());

        \shuffle($users);

        if (\count($users) % 2 !== 0) {
            $users[] = null;
        }

        $fixtures = [];
        $halfCount = \count($users) / 2;
        $position = 0;

        for ($i = 0; $i < \count($users) - 1; $i++) {
            for ($j = 0; $j <= $halfCount - 1; $j++) {
                $user1 = $users[$j];
                $user2 = $users[\count($users) - $j - 1];

                if (null === $user1 || null === $user2) {
                    continue;
                }

                $fixtures[] = new KeyforgeCompetitionFixture(
                    Uuid::v4(),
                    $competition->id(),
                    'Jornada ' . $i + 1,
                    [$user1, $user2],
                    CompetitionFixtureType::BEST_OF_1,
                    $position,
                    new \DateTimeImmutable(),
                    null,
                    null,
                    null,
                );

                $position++;
            }

            $users = $this->rotate($users);
        }

        return $fixtures;
    }

    private function rotate(array $users): array {
        $firstPlayer = $users[0];
        unset($users[0]);

        $lastPlayer = \array_pop($users);

        return [
            $firstPlayer,
            $lastPlayer,
            ...$users,
        ];
    }
}
