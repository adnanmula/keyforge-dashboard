<?php declare(strict_types=1);

namespace AdnanMula\Cards\Application\Keyforge\AddGame;

use AdnanMula\Cards\Domain\Model\Keyforge\KeyforgeGame;
use AdnanMula\Cards\Domain\Model\Keyforge\KeyforgeRepository;
use AdnanMula\Cards\Domain\Model\Keyforge\ValueObject\KeyforgeGameScore;
use AdnanMula\Cards\Domain\Model\Shared\ValueObject\UuidValueObject;

final class AddKeyforgeGameCommandHandler
{
    public function __construct(
        private KeyforgeRepository $repository
    ) {}

    public function __invoke(AddKeyforgeGameCommand $command): void
    {
        $game = new KeyforgeGame(
            UuidValueObject::v4(),
            $command->winner(),
            $command->loser(),
            $command->winnerDeck(),
            $command->loserDeck(),
            $command->firstTurn(),
            KeyforgeGameScore::from(3, $command->loserScore()),
            $command->date(),
        );

        $this->repository->saveGame($game);
    }
}
