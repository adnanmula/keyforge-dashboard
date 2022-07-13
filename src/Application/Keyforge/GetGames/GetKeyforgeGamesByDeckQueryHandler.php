<?php declare(strict_types=1);

namespace AdnanMula\Cards\Application\Keyforge\GetGames;

use AdnanMula\Cards\Domain\Model\Keyforge\KeyforgeDeck;
use AdnanMula\Cards\Domain\Model\Keyforge\KeyforgeRepository;
use AdnanMula\Cards\Domain\Model\User\UserRepository;

final class GetKeyforgeGamesByDeckQueryHandler
{
    private UserRepository $userRepository;
    private KeyforgeRepository $repository;

    public function __construct(UserRepository $userRepository, KeyforgeRepository $repository)
    {
        $this->userRepository = $userRepository;
        $this->repository = $repository;
    }

    /** @return array<KeyforgeDeck> */
    public function __invoke(GetKeyforgeGamesByDeckQuery $query): array
    {
        $games = $this->repository->gamesByDeck($query->deckId());

        $userIds = [];
        $decksIds = [];

        foreach ($games as $game) {
            $userIds[] = $game->winner();
            $userIds[] = $game->loser();
            $decksIds[] = $game->winnerDeck();
            $decksIds[] = $game->loserDeck();
        }

        $decks = $this->repository->byIds(...$decksIds);
        $users = $this->userRepository->byIds(...$userIds);

        $indexedDecks = [];
        foreach ($decks as $deck) {
            $indexedDecks[$deck->id()->value()] = $deck->name();
        }

        $indexedUsers = [];
        foreach ($users as $user) {
            $indexedUsers[$user->id()->value()] = $user->name();
        }

        $result = [];

        foreach ($games as $game) {
            $result[] = [
                'winner' => $game->winner()->value(),
                'winner_name' => $indexedUsers[$game->winner()->value()],
                'winner_deck' => $game->winnerDeck()->value(),
                'winner_deck_name' => $indexedDecks[$game->winnerDeck()->value()],
                'loser' => $game->loser()->value(),
                'loser_name' => $indexedUsers[$game->loser()->value()],
                'loser_deck' => $game->loserDeck()->value(),
                'loser_deck_name' => $indexedDecks[$game->loserDeck()->value()],
                'score' => $game->score()->winnerScore() . '/' . $game->score()->loserScore(),
                'first_turn' => null === $game->firstTurn() ? null : $indexedUsers[$game->firstTurn()->value()],
                'date' => $game->date()->format('Y-m-d'),
            ];
        }

        return $result;
    }
}
