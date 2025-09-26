<?php declare(strict_types=1);

namespace AdnanMula\Cards\Application\Command\Keyforge\Game\Analyze;

use AdnanMula\Cards\Domain\Model\Keyforge\Game\KeyforgeGameLog;
use AdnanMula\Cards\Domain\Model\Keyforge\Game\KeyforgeGameRepository;
use AdnanMula\Cards\Domain\Model\Shared\User;
use AdnanMula\KeyforgeGameLogParser\GameLogParser;
use Symfony\Bundle\SecurityBundle\Security;

final readonly class AnalyzeGameCommandHandler
{
    public function __construct(
        private KeyforgeGameRepository $repository,
        private Security $security,
    ) {}

    public function __invoke(AnalyzeGameCommand $command): void
    {
        /** @var ?User $user */
        $user = $this->security->getUser();

        $gameLog = new KeyforgeGameLog(
            $command->id,
            $command->gameId,
            new GameLogParser()->execute($command->log)->rawLog,
            $user?->id(),
            new \DateTimeImmutable(),
        );

        $this->repository->saveLog($gameLog);
    }
}
