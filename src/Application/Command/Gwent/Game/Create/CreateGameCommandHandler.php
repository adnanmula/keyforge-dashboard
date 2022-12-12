<?php declare(strict_types=1);

namespace AdnanMula\Cards\Application\Command\Gwent\Game\Create;

use AdnanMula\Cards\Domain\Model\Gwent\GwentDeckRepository;
use AdnanMula\Cards\Domain\Model\Gwent\GwentGame;
use AdnanMula\Cards\Domain\Model\Gwent\GwentGameRepository;
use AdnanMula\Cards\Domain\Model\Shared\ValueObject\Uuid;

final class CreateGameCommandHandler
{
    public function __construct(
        private GwentDeckRepository $deckRepository,
        private GwentGameRepository $gameRepository,
    ) {}

    public function __invoke(CreateGameCommand $command): void
    {
        $this->assert($command);

        $this->gameRepository->save(new GwentGame(
            Uuid::v4(),
            $command->userId(),
            $command->userDeck(),
            $command->opponentDeckArchetype(),
            $command->win(),
            $command->rank(),
            $command->coin(),
            $command->score(),
            $command->date(),
            new \DateTimeImmutable(),
        ));
    }

    private function assert(CreateGameCommand $command): void
    {
        $deck = $this->deckRepository->byId($command->userDeck());

        if (null === $deck) {
            throw new \Exception('Deck not found');
        }

        if (null !== $command->opponentDeckArchetype()) {
            $archetype = $this->deckRepository->archetypeById($command->opponentDeckArchetype());

            if (null === $archetype) {
                throw new \Exception('Archetype not found');
            }
        }
    }
}
