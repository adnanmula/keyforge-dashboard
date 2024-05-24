<?php declare(strict_types=1);

namespace AdnanMula\Cards\Application\Command\Keyforge\Deck\Import;

use AdnanMula\Cards\Domain\Service\Keyforge\ImportDeckService;
use AdnanMula\Cards\Infrastructure\Service\Keyforge\DoK\ImportMyDecksFromDokService;

final class ImportDeckCommandHandler
{
    public function __construct(
        private ImportDeckService $service,
        private ImportMyDecksFromDokService $myDecksService,
    ) {}

    public function __invoke(ImportDeckCommand $command): void
    {
        if (null !== $command->token) {
            $this->myDecksService->execute($command->token, $command->userId, true);
        }

        if (null !== $command->deckId) {
            $this->service->execute($command->deckId, $command->userId, true, true);
        }
    }
}
