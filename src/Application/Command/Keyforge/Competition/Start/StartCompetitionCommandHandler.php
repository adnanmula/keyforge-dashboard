<?php declare(strict_types=1);

namespace AdnanMula\Cards\Application\Command\Keyforge\Competition\Start;

use AdnanMula\Cards\Domain\Model\Keyforge\Game\KeyforgeCompetition;
use AdnanMula\Cards\Domain\Model\Keyforge\Game\KeyforgeCompetitionRepository;

final class StartCompetitionCommandHandler
{
    public function __construct(
        private KeyforgeCompetitionRepository $repository,
    ) {}

    public function __invoke(StartCompetitionCommand $command): void
    {
        $competition = $this->repository->byId($command->competitionId);

        $this->assert($competition);

        $competition->updateStartDate($command->date);

        $this->repository->save($competition);
    }

    private function assert(?KeyforgeCompetition $competition): void
    {
        if (null === $competition) {
            throw new \Exception('Competition not found');
        }

        if (null !== $competition->startedAt()) {
            throw new \Exception('Competition already started');
        }

        if (null !== $competition->finishedAt()) {
            throw new \Exception('Competition already finished');
        }
    }
}
