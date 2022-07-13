<?php declare(strict_types=1);

namespace AdnanMula\Cards\Application\Keyforge\AddGame;

use AdnanMula\Cards\Domain\Model\Keyforge\KeyforgeDeck;
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
        $decks = $this->repository->byNames(
            $command->winnerDeck(),
            $command->loserDeck(),
        );

        $winnerDeck = null;
        $loserDeck = null;

        foreach ($decks as $deck) {
            if ($deck->name() === $command->winnerDeck()) {
                $winnerDeck = $deck;

                continue;
            }

            if ($deck->name() === $command->loserDeck()) {
                $loserDeck = $deck;
            }
        }

        if (null === $winnerDeck || null === $loserDeck) {
            throw new \InvalidArgumentException('Deck not found');
        }

        $game = new KeyforgeGame(
            UuidValueObject::v4(),
            $command->winner(),
            $command->loser(),
            $winnerDeck->id(),
            $loserDeck->id(),
            $command->firstTurn(),
            KeyforgeGameScore::from(3, $command->loserScore()),
            $command->date(),
        );

        $this->repository->saveGame($game);

        $this->updateDeckWinRate($winnerDeck, $loserDeck);
    }

    private function updateDeckWinRate(KeyforgeDeck $winnerDeck, KeyforgeDeck $loserDeck): void
    {
        $games1 = $this->repository->gamesByDeck($winnerDeck->id());
        $games2 = $this->repository->gamesByDeck($loserDeck->id());

        $deck1Wins = 0;
        $deck1Loses = 0;

        foreach ($games1 as $game) {
            if ($game->winnerDeck()->equalTo($winnerDeck->id())) {
                $deck1Wins++;
            }

            if ($game->loserDeck()->equalTo($winnerDeck->id())) {
                $deck1Loses++;
            }
        }

        $deck2Wins = 0;
        $deck2Loses = 0;

        foreach ($games2 as $game) {
            if ($game->winnerDeck()->equalTo($loserDeck->id())) {
                $deck2Wins++;
            }

            if ($game->loserDeck()->equalTo($loserDeck->id())) {
                $deck2Loses++;
            }
        }

        $winnerDeck->updateWins($deck1Wins)->updateLoses($deck1Loses);
        $loserDeck->updateWins($deck2Wins)->updateLoses($deck2Loses);

        $this->repository->save($winnerDeck);
        $this->repository->save($loserDeck);
    }
}
