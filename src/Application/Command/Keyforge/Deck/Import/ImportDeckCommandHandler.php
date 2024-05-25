<?php declare(strict_types=1);

namespace AdnanMula\Cards\Application\Command\Keyforge\Deck\Import;

use AdnanMula\Cards\Application\Command\Keyforge\Stat\General\GenerateGeneralStatsCommand;
use AdnanMula\Cards\Application\Command\Keyforge\Stat\User\GenerateUserStatsCommand;
use AdnanMula\Cards\Domain\Service\Keyforge\ImportDeckService;
use AdnanMula\Cards\Infrastructure\Service\Keyforge\DoK\ImportMyDecksFromDokService;
use Symfony\Component\Messenger\MessageBusInterface;

final class ImportDeckCommandHandler
{
    public function __construct(
        private ImportDeckService $service,
        private ImportMyDecksFromDokService $myDecksService,
        private MessageBusInterface $bus,
    ) {}

    public function __invoke(ImportDeckCommand $command): void
    {
        if (null !== $command->token) {
            $this->myDecksService->execute($command->token, $command->userId, true);
        }

        if (null !== $command->deckId) {
            $this->service->execute($command->deckId, $command->userId, true, true);
        }

        $this->bus->dispatch(new GenerateGeneralStatsCommand());

        if (null !== $command->userId) {
            $this->bus->dispatch(new GenerateUserStatsCommand($command->userId->value()));
        }
    }
}
