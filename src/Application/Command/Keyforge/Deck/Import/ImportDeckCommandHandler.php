<?php declare(strict_types=1);

namespace AdnanMula\Cards\Application\Command\Keyforge\Deck\Import;

use AdnanMula\Cards\Domain\Model\Keyforge\Stat\KeyforgeStatRepository;
use AdnanMula\Cards\Domain\Model\Keyforge\Stat\ValueObject\KeyforgeStatCategory;
use AdnanMula\Cards\Domain\Service\Keyforge\ImportDeckService;
use AdnanMula\Cards\Infrastructure\Service\Keyforge\DoK\ImportMyDecksFromDokService;

final class ImportDeckCommandHandler
{
    public function __construct(
        private ImportDeckService $service,
        private ImportMyDecksFromDokService $myDecksService,
        private KeyforgeStatRepository $statRepository,
    ) {}

    public function __invoke(ImportDeckCommand $command): void
    {
        if (null !== $command->token) {
            $this->myDecksService->execute($command->token, $command->userId, true);
        }

        if (null !== $command->deckId) {
            $this->service->execute($command->deckId, $command->userId, true, true);
        }

        $this->statRepository->queueProjection(KeyforgeStatCategory::HOME_GENERAL_DATA, null);
        $this->statRepository->queueProjection(KeyforgeStatCategory::USER_PROFILE, $command->userId);
    }
}
