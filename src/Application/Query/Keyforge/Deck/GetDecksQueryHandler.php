<?php declare(strict_types=1);

namespace AdnanMula\Cards\Application\Query\Keyforge\Deck;

use AdnanMula\Cards\Domain\Model\Keyforge\KeyforgeDeck;
use AdnanMula\Cards\Domain\Model\Keyforge\KeyforgeDeckRepository;
use AdnanMula\Cards\Domain\Model\Keyforge\KeyforgeGame;
use AdnanMula\Cards\Domain\Model\Keyforge\KeyforgeGameRepository;
use AdnanMula\Cards\Domain\Model\Shared\ValueObject\Uuid;
use AdnanMula\Cards\Infrastructure\Criteria\Criteria;
use AdnanMula\Cards\Infrastructure\Criteria\Filter\Filter;
use AdnanMula\Cards\Infrastructure\Criteria\Filter\Filters;
use AdnanMula\Cards\Infrastructure\Criteria\Filter\FilterType;
use AdnanMula\Cards\Infrastructure\Criteria\FilterField\ArrayElementFilterField;
use AdnanMula\Cards\Infrastructure\Criteria\FilterField\FilterField;
use AdnanMula\Cards\Infrastructure\Criteria\FilterValue\FilterOperator;
use AdnanMula\Cards\Infrastructure\Criteria\FilterValue\NullFilterValue;
use AdnanMula\Cards\Infrastructure\Criteria\FilterValue\StringFilterValue;
use AdnanMula\Cards\Infrastructure\Criteria\Sorting\Order;
use AdnanMula\Cards\Infrastructure\Criteria\Sorting\OrderType;

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

        $expressions = [];

        if (null !== $query->owner()) {
            $expressions[] = new Filter(new FilterField('owner'), new StringFilterValue($query->owner()->value()), FilterOperator::EQUAL);
        }

        if (null !== $query->set()) {
            $expressions[] = new Filter(new FilterField('set'), new StringFilterValue($query->set()), FilterOperator::CONTAINS);
        }

        if (null !== $query->deck()) {
            $expressions[] = new Filter(new FilterField('name'), new StringFilterValue($query->deck()), FilterOperator::CONTAINS);
        }

        if ($query->onlyOwned()) {
            $expressions[] = new Filter(new FilterField('owner'), new NullFilterValue(), FilterOperator::IS_NOT_NULL);
        }

        $filters = [new Filters(FilterType::AND, FilterType::AND, ...$expressions)];

        if (null !== $query->house()) {
            $filters[] = new Filters(
                FilterType::AND,
                FilterType::OR,
                new Filter(new ArrayElementFilterField('houses', 0), new StringFilterValue($query->house()), FilterOperator::EQUAL),
                new Filter(new ArrayElementFilterField('houses', 1), new StringFilterValue($query->house()), FilterOperator::EQUAL),
                new Filter(new ArrayElementFilterField('houses', 2), new StringFilterValue($query->house()), FilterOperator::EQUAL),
            );
        }

        $criteria = new Criteria(
            $query->sorting(),
            $query->start(),
            $query->length(),
            ...$filters,
        );

        $decks = $this->repository->search($criteria);

//      TODO ñapa
        if (null !== $query->owner()) {
            $decks = $this->recalculateWins($query->owner(), $query->deckId(), ...$decks);

            if (null !== $query->sorting() && $query->sorting()->has('wins')) {
                $decks = $this->reorderDecks($query->sorting()->get('wins'), ...$decks);
            }
        }

        $countCriteria = new Criteria(
            null,
            null,
            null,
            ...$criteria->filters(),
        );

        $total = $this->repository->count(new Criteria(null, null, null));
        $totalFiltered = $this->repository->count($countCriteria);

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
        $filters = [new Filters(
            FilterType::AND,
            FilterType::OR,
            new Filter(new FilterField('winner'), new StringFilterValue($userId->value()), FilterOperator::EQUAL),
            new Filter(new FilterField('loser'), new StringFilterValue($userId->value()), FilterOperator::EQUAL),
        )];

        if (null !== $deckId) {
            $filters[] = new Filters(
                FilterType::AND,
                FilterType::OR,
                new Filter(new FilterField('winner_deck'), new StringFilterValue($deckId->value()), FilterOperator::EQUAL),
                new Filter(new FilterField('loser_deck'), new StringFilterValue($deckId->value()), FilterOperator::EQUAL),
            );
        }

        foreach ($decks as $deck) {
            $games = $this->gameRepository->search(new Criteria(null, null, null, ...$filters));

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
    private function reorderDecks(Order $order, KeyforgeDeck ...$decks): array
    {
        if ($order->type() === OrderType::DESC) {
            \usort($decks, static function (KeyforgeDeck $a, KeyforgeDeck $b) {
                if ($a->wins() === $b->wins()) {
                    return $a->losses() <=> $b->losses();
                }

                return $b->wins() <=> $a->wins();
            });
        }

        if ($order->type() === OrderType::ASC) {
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
