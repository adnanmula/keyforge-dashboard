<?php declare(strict_types=1);

namespace AdnanMula\Cards\Application\Query\Keyforge\Deck;

use AdnanMula\Cards\Domain\Model\Keyforge\KeyforgeDeck;
use AdnanMula\Cards\Domain\Model\Keyforge\KeyforgeDeckRepository;
use AdnanMula\Cards\Domain\Model\Keyforge\KeyforgeGame;
use AdnanMula\Cards\Domain\Model\Keyforge\KeyforgeGameRepository;
use AdnanMula\Cards\Domain\Model\Shared\Filter;
use AdnanMula\Cards\Domain\Model\Shared\QueryOrder;
use AdnanMula\Cards\Domain\Model\Shared\SearchTerm;
use AdnanMula\Cards\Domain\Model\Shared\SearchTerms;
use AdnanMula\Cards\Domain\Model\Shared\SearchTermType;
use AdnanMula\Cards\Domain\Model\Shared\ValueObject\Uuid;

final class GetDecksQueryHandler
{
    public function __construct(
        private KeyforgeDeckRepository $repository,
        private KeyforgeGameRepository $gameRepository,
    ) {}

    public function __invoke(GetDecksQuery $query): array
    {
        if (null !== $query->deckId()) {
            $deck = $this->repository->byId($query->deckId());

            return [
                'decks' => [$deck],
                'total' => 1,
                'totalFiltered' => 1,
                'start' => $query->start(),
                'length' => $query->length(),
            ];
        }

        $decks = $this->repository->all(
            $query->start(),
            $query->length(),
            $query->deck(),
            $query->set(),
            $query->house(),
            $query->owner(),
            $query->order(),
        );

//      TODO ñapa
        if (null !== $query->owner()) {
            $decks = $this->recalculateWins($query->owner(), $query->deckId(), ...$decks);

            if (null !== $query->order() && $query->order()->field() === 'win_rate') {
                $decks = $this->reorderDecks($query->order(), ...$decks);
            }
        }

        $total = $this->repository->count();
        $totalFiltered = $this->repository->count($query->deck(), $query->set(), $query->house(), $query->owner());

        return [
            'decks' => $decks,
            'total' => $total,
            'totalFiltered' => $totalFiltered,
            'start' => $query->start(),
            'length' => $query->length(),
        ];
    }

    /** @return array<KeyforgeDeck> */
    private function recalculateWins(Uuid $userId, ?Uuid $deckId, KeyforgeDeck ...$decks): array
    {
        $filters = [new SearchTerm(
            SearchTermType::OR,
            new Filter('winner', $userId->value()),
            new Filter('loser', $userId->value()),
        )];

        if (null !== $deckId) {
            $filters[] = new SearchTerm(
                SearchTermType::OR,
                new Filter('winner_deck', $deckId->value()),
                new Filter('loser_deck', $deckId->value()),
            );
        }

        foreach ($decks as $deck) {
            $games = $this->gameRepository->search(new SearchTerms(
                SearchTermType::AND,
                ...$filters,
            ), null, null);

            $deckWins = 0;
            $deckLosses = 0;

            /** @var KeyforgeGame $game */
            foreach ($games as $game) {
                if (false === $game->winnerDeck()->equalTo($deck->id())
                    && false === $game->loserDeck()->equalTo($deck->id())) {
                    continue;
                }

                if ($game->winner()->equalTo($userId) && $game->winnerDeck()->equalTo($deck->id())) {
                    $deckWins++;
                }

                if ($game->loser()->equalTo($userId) && $game->loserDeck()->equalTo($deck->id())) {
                    $deckLosses++;
                }
            }

            $deck->updateWins($deckWins);
            $deck->updateLosses($deckLosses);
        }

        return $decks;
    }

    /** @return array<KeyforgeDeck> */
    private function reorderDecks(QueryOrder $order, KeyforgeDeck ...$decks): array
    {
        if ($order->order() === 'desc') {
            \usort($decks, static function (KeyforgeDeck $a, KeyforgeDeck $b) {
                if ($a->wins() === $b->wins()) {
                    return $a->losses() <=> $b->losses();
                }

                return $b->wins() <=> $a->wins();
            });
        }

        if ($order->order() === 'asc') {
            \usort($decks, static function (KeyforgeDeck $a, KeyforgeDeck $b) {
                if ($b->wins() === $a->wins()) {
                    return $b->losses() <=> $a->losses();
                }

                return $a->wins() <=> $b->wins();
            });
        }

        return $decks;
    }
}
