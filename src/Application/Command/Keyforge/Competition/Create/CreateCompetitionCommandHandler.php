<?php declare(strict_types=1);

namespace AdnanMula\Cards\Application\Command\Keyforge\Competition\Create;

use AdnanMula\Cards\Domain\Model\Keyforge\Game\KeyforgeCompetition;
use AdnanMula\Cards\Domain\Model\Keyforge\Game\KeyforgeCompetitionRepository;
use AdnanMula\Cards\Domain\Model\Shared\ValueObject\Uuid;

final readonly class CreateCompetitionCommandHandler
{
    public function __construct(
        private KeyforgeCompetitionRepository $repository,
    ) {}

    public function __invoke(CreateCompetitionCommand $command): void
    {
        $this->assertType($command);

        $competition = new KeyforgeCompetition(
            Uuid::v4(),
            $command->name,
            $command->type,
            [],
            $command->admins,
            $command->description,
            $command->visibility,
            new \DateTimeImmutable(),
            null,
            null,
            null,
        );

        $this->repository->save($competition);
    }

    private function assertType(CreateCompetitionCommand $command): void
    {
        if (false === $command->type->isRoundRobin()) {
            throw new \Exception('Type not supported yet, only Round Robin (1/2) are available');
        }
    }
}
