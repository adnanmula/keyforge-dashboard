<?php declare(strict_types=1);

namespace AdnanMula\Cards\Application\Command\Keyforge\Deck\Import;

use AdnanMula\Cards\Domain\Service\Keyforge\ImportDeckService;

final class ImportDeckCommandHandler
{
    public function __construct(
        private ImportDeckService $service,
    ) {}

    public function __invoke(ImportDeckCommand $command): void
    {
        $this->service->execute($command->deckId());
    }
}
