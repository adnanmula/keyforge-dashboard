<?php declare(strict_types=1);

namespace AdnanMula\Cards\Application\Command\Keyforge\Game\Create;

use AdnanMula\Cards\Domain\Model\Keyforge\KeyforgeDeck;
use AdnanMula\Cards\Domain\Model\Keyforge\KeyforgeDeckRepository;
use AdnanMula\Cards\Domain\Model\Keyforge\KeyforgeGame;
use AdnanMula\Cards\Domain\Model\Keyforge\KeyforgeGameRepository;
use AdnanMula\Cards\Domain\Model\Keyforge\ValueObject\KeyforgeGameScore;
use AdnanMula\Cards\Domain\Model\Shared\ValueObject\Uuid;

final class CreateGameCommandHandler
{
    public function __construct(
        private KeyforgeGameRepository $gameRepository,
        private KeyforgeDeckRepository $deckRepository,
    ) {}

    public function __invoke(CreateGameCommand $command): void
    {
        $decks = $this->deckRepository->byNames(
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
            Uuid::v4(),
            $command->winner(),
            $command->loser(),
            $winnerDeck->id(),
            $loserDeck->id(),
            $command->firstTurn(),
            KeyforgeGameScore::from(3, $command->loserScore()),
            $command->date(),
        );

        $this->gameRepository->save($game);

        $this->updateDeckWinRate($winnerDeck, $loserDeck);
    }

    private function updateDeckWinRate(KeyforgeDeck $winnerDeck, KeyforgeDeck $loserDeck): void
    {
        $games1 = $this->gameRepository->byDeck($winnerDeck->id());
        $games2 = $this->gameRepository->byDeck($loserDeck->id());

        $deck1Wins = 0;
        $deck1Losses = 0;

        foreach ($games1 as $game) {
            if ($game->winnerDeck()->equalTo($winnerDeck->id())) {
                $deck1Wins++;
            }

            if ($game->loserDeck()->equalTo($winnerDeck->id())) {
                $deck1Losses++;
            }
        }

        $deck2Wins = 0;
        $deck2Losses = 0;

        foreach ($games2 as $game) {
            if ($game->winnerDeck()->equalTo($loserDeck->id())) {
                $deck2Wins++;
            }

            if ($game->loserDeck()->equalTo($loserDeck->id())) {
                $deck2Losses++;
            }
        }

        $winnerDeck->updateWins($deck1Wins)->updateLosses($deck1Losses);
        $loserDeck->updateWins($deck2Wins)->updateLosses($deck2Losses);

        $this->deckRepository->save($winnerDeck);
        $this->deckRepository->save($loserDeck);
    }
}
