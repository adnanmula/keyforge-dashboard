<?php declare(strict_types=1);

namespace AdnanMula\Cards\Application\Query\Keyforge\Game;

use AdnanMula\Cards\Domain\Model\Keyforge\KeyforgeDeckRepository;
use AdnanMula\Cards\Domain\Model\Keyforge\KeyforgeGameRepository;
use AdnanMula\Cards\Domain\Model\Keyforge\KeyforgeUserRepository;
use AdnanMula\Cards\Infrastructure\Criteria\Criteria;

final class GetGamesQueryHandler
{
    public function __construct(
        private KeyforgeGameRepository $gameRepository,
        private KeyforgeDeckRepository $deckRepository,
        private KeyforgeUserRepository $userRepository,
    ) {}

    public function __invoke(GetGamesQuery $query): array
    {
        $games = $this->gameRepository->search($query->criteria());

        $criteriaWithoutOrder = new Criteria(
            null,
            null,
            null,
            ...$query->criteria()->filters(),
        );

        $total = $this->gameRepository->count($criteriaWithoutOrder);

        $userIds = [];
        $decksIds = [];

        foreach ($games as $game) {
            $userIds[] = $game->winner();
            $userIds[] = $game->loser();
            $decksIds[] = $game->winnerDeck();
            $decksIds[] = $game->loserDeck();
        }

        $decks = $this->deckRepository->byIds(...$decksIds);
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

        return [
            'games' => $result,
            'total' => $total,
            'totalFiltered' => $total,
        ];
    }
}
