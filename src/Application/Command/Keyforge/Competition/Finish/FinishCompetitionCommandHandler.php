<?php declare(strict_types=1);

namespace AdnanMula\Cards\Application\Command\Keyforge\Competition\Finish;

use AdnanMula\Cards\Domain\Model\Keyforge\Game\KeyforgeCompetition;
use AdnanMula\Cards\Domain\Model\Keyforge\Game\KeyforgeCompetitionRepository;

final class FinishCompetitionCommandHandler
{
    public function __construct(
        private KeyforgeCompetitionRepository $repository,
    ) {}

    public function __invoke(FinishCompetitionCommand $command): void
    {
        $competition = $this->repository->byId($command->competitionId);

        $this->assert($competition);

        $competition->updateFinishDate($command->date);
        $competition->updateWinner($command->winnerId);

        $this->repository->save($competition);
    }

    private function assert(?KeyforgeCompetition $competition): void
    {
        if (null === $competition) {
            throw new \Exception('Competition not found');
        }

        if (null === $competition->startedAt()) {
            throw new \Exception('Competition not started');
        }

        if (null !== $competition->finishedAt()) {
            throw new \Exception('Competition already finished');
        }
    }
}
