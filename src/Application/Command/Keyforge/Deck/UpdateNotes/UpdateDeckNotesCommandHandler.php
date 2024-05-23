<?php declare(strict_types=1);

namespace AdnanMula\Cards\Application\Command\Keyforge\Deck\UpdateNotes;

use AdnanMula\Cards\Domain\Model\Keyforge\KeyforgeDeckRepository;
use AdnanMula\Cards\Domain\Model\Keyforge\ValueObject\KeyforgeDeckUserData;

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

        if (null === $deck->userData()->owner || false === $deck->userData()->owner->equalTo($command->userId)) {
            throw new \Exception('You are not the owner of this deck.');
        }

        $this->repository->saveDeckUserData(KeyforgeDeckUserData::from(
            $deck->userData()->id,
            $deck->userData()->owner,
            $deck->userData()->wins,
            $deck->userData()->losses,
            $command->notes,
        ));
    }
}
