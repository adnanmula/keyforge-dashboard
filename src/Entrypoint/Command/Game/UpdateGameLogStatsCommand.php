<?php declare(strict_types=1);

namespace AdnanMula\Cards\Entrypoint\Command\Game;

use AdnanMula\Cards\Domain\Model\Keyforge\Game\KeyforgeGameLog;
use AdnanMula\Cards\Domain\Model\Keyforge\Game\KeyforgeGameRepository;
use AdnanMula\Cards\Domain\Model\Shared\ValueObject\Uuid;
use AdnanMula\KeyforgeGameLogParser\Event\EventType;
use AdnanMula\KeyforgeGameLogParser\Parser\GameLogParser;
use AdnanMula\KeyforgeGameLogParser\Parser\ParseType;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(name: 'game:log:update', description: 'Recompute and persist denormalized stats on all game logs')]
final class UpdateGameLogStatsCommand extends Command
{
    public function __construct(
        private readonly KeyforgeGameRepository $repository,
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this->addOption('log', 'l', InputOption::VALUE_REQUIRED, 'Process a single game log by UUID');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $logId = $input->getOption('log');

        if (null !== $logId) {
            $logs = \array_filter([$this->repository->gameLog(Uuid::from($logId))]);
        } else {
            $logs = $this->repository->allLogs();
        }

        $total = \count($logs);
        $progressBar = new ProgressBar($output, $total);
        $progressBar->start();

        $updated = 0;
        $skipped = 0;
        $parser = new GameLogParser();

        foreach ($logs as $gameLog) {
            if (null === $gameLog->log) {
                ++$skipped;
                $progressBar->advance();

                continue;
            }

            try {
                $parsedGame = $parser->execute($gameLog->log, ParseType::ARRAY);
            } catch (\Throwable) {
                ++$skipped;
                $progressBar->advance();

                if ($output->isVerbose()) {
                    $output->writeln(' | SKIP (parse error) ' . $gameLog->id->value());
                }

                continue;
            }

            $winner = $parsedGame->winner();
            $loser = $parsedGame->loser();
            $wt = $winner?->timeline;
            $lt = $loser?->timeline;

            $updatedLog = new KeyforgeGameLog(
                id: $gameLog->id,
                gameId: $gameLog->gameId,
                log: $gameLog->log,
                createdBy: $gameLog->createdBy,
                createdAt: $gameLog->createdAt,
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

            $this->repository->saveLog($updatedLog);
            ++$updated;

            $progressBar->advance();

            if ($output->isVerbose()) {
                $output->writeln(' | ' . $gameLog->id->value());
            }
        }

        $progressBar->finish();
        $output->writeln('');

        $io->success(sprintf('Updated %d log(s), skipped %d', $updated, $skipped));

        return self::SUCCESS;
    }
}
