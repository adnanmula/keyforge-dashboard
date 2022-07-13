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

        $decks = $this->repository->byIds(
            $game->winnerDeck(),
            $game->loserDeck()
        );

        $deck1 = null;
        foreach ($decks as $deck) {
            if ($deck->id()->equalTo($game->winnerDeck())) {
                $deck1 = $deck;

                break;
            }
        }

        $deck2 = null;
        foreach ($decks as $deck) {
            if ($deck->id()->equalTo($game->loserDeck())) {
                $deck2 = $deck;

                break;
            }
        }

        $games1 = $this->repository->gamesByDeck($deck1->id());
        $games2 = $this->repository->gamesByDeck($deck2->id());

        $deck1Wins = 0;
        $deck1Loses = 0;

        foreach ($games1 as $game) {
            if ($game->winnerDeck()->equalTo($deck1->id())) {
                $deck1Wins++;
            }

            if ($game->loserDeck()->equalTo($deck1->id())) {
                $deck1Loses++;
            }
        }

        $deck2Wins = 0;
        $deck2Loses = 0;

        foreach ($games2 as $game) {
            if ($game->winnerDeck()->equalTo($deck2->id())) {
                $deck2Wins++;
            }

            if ($game->loserDeck()->equalTo($deck2->id())) {
                $deck2Loses++;
            }
        }

        $deck1->updateWins($deck1Wins)->updateLoses($deck1Loses);
        $deck2->updateWins($deck2Wins)->updateLoses($deck2Loses);

        $this->repository->save($deck1);
        $this->repository->save($deck2);
    }
}
