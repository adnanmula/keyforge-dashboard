<?php declare(strict_types=1);

namespace AdnanMula\Cards\Application\Command\Keyforge\Deck\Import;

use AdnanMula\Cards\Domain\Service\Keyforge\ImportDeckAllianceService;
use AdnanMula\Cards\Domain\Service\Keyforge\ImportDeckService;
use AdnanMula\Cards\Infrastructure\Service\Keyforge\DoK\ImportMyDecksFromDokService;
use Psr\Log\LoggerInterface;

final readonly class ImportDeckCommandHandler
{
    public function __construct(
        private ImportDeckService $service,
        private ImportDeckAllianceService $allianceService,
        private ImportMyDecksFromDokService $myDecksService,
        private LoggerInterface $userActivityLogger,
    ) {}

    public function __invoke(ImportDeckCommand $command): void
    {
        $this->{'import'.\ucfirst(\strtolower($command->deckType->value))}($command);

        $this->userActivityLogger->info('Deck imported', [
            'user' => $command->userId?->value(),
            'deckId' => $command->deckId?->value(),
            'deckType' => $command->deckType->name,
            'is_with_token' => null !== $command->token,
        ]);
    }

    private function importStandard(ImportDeckCommand $command): void
    {
        if (null !== $command->token) {
            $this->myDecksService->execute($command->token, $command->userId, true);
        }

        if (null !== $command->deckId) {
            $this->service->execute($command->deckId, $command->userId, true, true);
        }
    }

    private function importAlliance(ImportDeckCommand $command): void
    {
        $this->allianceService->execute($command->deckId, $command->userId, true);
    }

    private function importTheoretical(ImportDeckCommand $command): void
    {
        throw new \Exception('Not implemented');
    }
}
