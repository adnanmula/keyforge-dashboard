<?php declare(strict_types=1);

namespace AdnanMula\Cards\Application\Command\Keyforge\Deck\UpdateNotes;

use AdnanMula\Cards\Domain\Model\Keyforge\KeyforgeDeckRepository;

final class UpdateDeckNotesCommandHandler
{
    public function __construct(
        private KeyforgeDeckRepository $repository,
    ) {}

    public function __invoke(UpdateDeckNotesCommand $command): void
    {
        $deck = $this->repository->byId($command->deckId);

        if (null === $deck) {
            throw new \Exception('Deck not found.');
        }

        if (null === $deck->owner() || false === $deck->owner()->equalTo($command->userId)) {
            throw new \Exception('You are not the owner of this deck.');
        }

        $deck->updateNotes($command->notes);

        $this->repository->save($deck);
    }
}
