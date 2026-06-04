<?php declare(strict_types=1);

namespace AdnanMula\Cards\Application\Command\Keyforge\Game\Analyze;

use AdnanMula\Cards\Domain\Model\Keyforge\Game\KeyforgeGameLog;
use AdnanMula\Cards\Domain\Model\Keyforge\Game\KeyforgeGameRepository;
use AdnanMula\Cards\Domain\Model\Shared\User;
use AdnanMula\KeyforgeGameLogParser\Event\EventType;
use AdnanMula\KeyforgeGameLogParser\Parser\GameLogParser;
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

        $parsedGame = (new GameLogParser())->execute($command->log);

        $winner = $parsedGame->winner();
        $loser = $parsedGame->loser();

        $wt = $winner?->timeline;
        $lt = $loser?->timeline;

        $gameLog = new KeyforgeGameLog(
            id: $command->id,
            gameId: $command->gameId,
            log: $parsedGame->rawLog,
            createdBy: $user?->id(),
            createdAt: new \DateTimeImmutable(),
            turns: $winner !== null ? $parsedGame->length : null,
            winnerAmberObtained: $wt?->totalAmberObtained(),
            winnerAmberStolen: $wt?->totalAmberStolen(),
            winnerCardsPlayed: $wt?->totalCardsPlayed(),
            winnerCardsDrawn: $wt?->totalCardsDrawn(),
            winnerCardsDiscarded: $wt?->totalCardsDiscarded(),
            winnerKeysForged: $wt?->filter(EventType::KEY_FORGED)->count(),
            winnerFights: $wt?->filter(EventType::FIGHT)->count(),
            winnerReaps: $wt?->filter(EventType::REAP)->count(),
            winnerExtraTurns: $wt?->totalExtraTurns(),
            loserAmberObtained: $lt?->totalAmberObtained(),
            loserAmberStolen: $lt?->totalAmberStolen(),
            loserCardsPlayed: $lt?->totalCardsPlayed(),
            loserCardsDrawn: $lt?->totalCardsDrawn(),
            loserCardsDiscarded: $lt?->totalCardsDiscarded(),
            loserKeysForged: $lt?->filter(EventType::KEY_FORGED)->count(),
            loserFights: $lt?->filter(EventType::FIGHT)->count(),
            loserReaps: $lt?->filter(EventType::REAP)->count(),
            loserExtraTurns: $lt?->totalExtraTurns(),
            totalAmberObtained: $wt !== null && $lt !== null ? $wt->totalAmberObtained() + $lt->totalAmberObtained() : null,
            totalAmberStolen: $wt !== null && $lt !== null ? $wt->totalAmberStolen() + $lt->totalAmberStolen() : null,
            totalCardsPlayed: $wt !== null && $lt !== null ? $wt->totalCardsPlayed() + $lt->totalCardsPlayed() : null,
            totalCardsDrawn: $wt !== null && $lt !== null ? $wt->totalCardsDrawn() + $lt->totalCardsDrawn() : null,
            totalCardsDiscarded: $wt !== null && $lt !== null ? $wt->totalCardsDiscarded() + $lt->totalCardsDiscarded() : null,
            totalKeysForged: $wt !== null && $lt !== null ? $wt->filter(EventType::KEY_FORGED)->count() + $lt->filter(EventType::KEY_FORGED)->count() : null,
            totalFights: $wt !== null && $lt !== null ? $wt->filter(EventType::FIGHT)->count() + $lt->filter(EventType::FIGHT)->count() : null,
            totalReaps: $wt !== null && $lt !== null ? $wt->filter(EventType::REAP)->count() + $lt->filter(EventType::REAP)->count() : null,
            totalExtraTurns: $wt !== null && $lt !== null ? $wt->totalExtraTurns() + $lt->totalExtraTurns() : null,
        );

        $this->repository->saveLog($gameLog);
    }
}
