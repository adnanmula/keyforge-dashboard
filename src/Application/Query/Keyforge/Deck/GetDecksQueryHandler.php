<?php declare(strict_types=1);

namespace AdnanMula\Cards\Application\Query\Keyforge\Deck;

use AdnanMula\Cards\Domain\Model\Keyforge\KeyforgeDeck;
use AdnanMula\Cards\Domain\Model\Keyforge\KeyforgeDeckRepository;
use AdnanMula\Cards\Domain\Model\Keyforge\KeyforgeGame;
use AdnanMula\Cards\Domain\Model\Keyforge\KeyforgeGameRepository;
use AdnanMula\Cards\Domain\Model\Keyforge\KeyforgeUser;
use AdnanMula\Cards\Domain\Model\Keyforge\KeyforgeUserRepository;
use AdnanMula\Cards\Domain\Model\Shared\ValueObject\Uuid;
use AdnanMula\Criteria\Criteria;
use AdnanMula\Criteria\Filter\Filter;
use AdnanMula\Criteria\Filter\Filters;
use AdnanMula\Criteria\Filter\FilterType;
use AdnanMula\Criteria\FilterField\FilterField;
use AdnanMula\Criteria\FilterValue\ArrayElementFilterValue;
use AdnanMula\Criteria\FilterValue\FilterOperator;
use AdnanMula\Criteria\FilterValue\IntFilterValue;
use AdnanMula\Criteria\FilterValue\NullFilterValue;
use AdnanMula\Criteria\FilterValue\StringFilterValue;
use AdnanMula\Criteria\Sorting\Order;
use AdnanMula\Criteria\Sorting\OrderType;

final class GetDecksQueryHandler
{
    public function __construct(
        private KeyforgeDeckRepository $repository,
        private KeyforgeUserRepository $userRepository,
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

        if (null !== $query->deck()) {
            $expressions[] = new Filter(new FilterField('name'), new StringFilterValue($query->deck()), FilterOperator::CONTAINS_INSENSITIVE);
        }

        if ($query->onlyOwned()) {
            $expressions[] = new Filter(new FilterField('owner'), new NullFilterValue(), FilterOperator::IS_NOT_NULL);
        }

        $expressions[] = new Filter(new FilterField('sas'), new IntFilterValue($query->maxSas()), FilterOperator::LESS_OR_EQUAL);
        $expressions[] = new Filter(new FilterField('sas'), new IntFilterValue($query->minSas()), FilterOperator::GREATER_OR_EQUAL);

        $filters = [new Filters(FilterType::AND, FilterType::AND, ...$expressions)];

        if (\count($query->owners()) > 0) {
            $ownerExpressions = [];

            foreach ($query->owners() as $owner) {
                $ownerExpressions[] = new Filter(new FilterField('owner'), new StringFilterValue($owner), FilterOperator::EQUAL);
            }

            $filters[] = new Filters(
                FilterType::AND,
                FilterType::OR,
                ...$ownerExpressions,
            );
        }

        if (\count($query->tags()) > 0) {
            $tagsExpressions = [];

            foreach ($query->tags() as $tag) {
                $tagsExpressions[] = new Filter(new FilterField('tags'), new ArrayElementFilterValue($tag->value()), FilterOperator::IN_ARRAY);
            }

            $filters[] = new Filters(
                FilterType::AND,
                $query->tagFilterType() === 'any' ? FilterType::OR : FilterType::AND,
                ...$tagsExpressions,
            );
        }

        if (\count($query->tagsExcluded()) > 0) {
            $tagsExpressions = [];

            foreach ($query->tagsExcluded() as $tag) {
                $tagsExpressions[] = new Filter(new FilterField('tags'), new ArrayElementFilterValue($tag->value()), FilterOperator::NOT_IN_ARRAY);
            }

            $filters[] = new Filters(
                FilterType::AND,
                FilterType::AND,
                ...$tagsExpressions,
            );
        }

        if (null !== $query->sets()) {
            $setFilterExpressions = [];

            foreach ($query->sets() as $set) {
                $setFilterExpressions[] = new Filter(new FilterField('set'), new StringFilterValue($set), FilterOperator::EQUAL);
            }

            $filters[] = new Filters(
                FilterType::AND,
                FilterType::OR,
                ...$setFilterExpressions,
            );
        }

        if (null !== $query->houses()) {
            $houseFilterExpressions = [];

            foreach ($query->houses() as $house) {
                $houseFilterExpressions[] = new Filter(new FilterField('houses'), new ArrayElementFilterValue($house), FilterOperator::IN_ARRAY);
            }

            $filters[] = new Filters(
                FilterType::AND,
                $query->houseFilterType() === 'any' ? FilterType::OR : FilterType::AND,
                ...$houseFilterExpressions,
            );
        }

        $criteria = new Criteria(
            $query->sorting(),
            $query->start(),
            $query->length(),
            ...$filters,
        );

        $decks = $this->repository->search($criteria);

//      TODO ñapas

        if (null !== $query->owner()) {
            $decks = $this->recalculateWins($query->owner(), $query->deckId(), ...$decks);

            if (null !== $query->sorting() && $query->sorting()->has('wins')) {
                $decks = $this->reorderDecks($query->sorting()->get('wins'), ...$decks);
            }
        }

        if ($query->onlyOwned()) {
            $decks = $this->removeNotOwnedStats($query->owner(), ...$decks);

            if (null !== $query->sorting() && $query->sorting()->has('wins')) {
                $decks = $this->reorderDecks($query->sorting()->get('wins'), ...$decks);
            }
        }

//      end ñapas

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
    private function removeNotOwnedStats(?Uuid $owner, KeyforgeDeck ...$decks): array
    {
        $users = $this->userRepository->all(false);
        $nonExternalUsersIds = \array_map(static fn (KeyforgeUser $user) => $user->id()->value(), $users);

        foreach ($decks as $deck) {
            $games = $this->gameRepository->search(new Criteria(null, null, null));

            $games = \array_values(\array_filter($games, static function (KeyforgeGame $game) use ($nonExternalUsersIds) {
                return \in_array($game->winner()->value(), $nonExternalUsersIds, true)
                    && \in_array($game->loser()->value(), $nonExternalUsersIds, true);
            }));

            $deckWins = 0;
            $deckLosses = 0;

            /** @var KeyforgeGame $game */
            foreach ($games as $game) {
                if (null !== $owner) {
                    $okWinner = $game->winnerDeck()->equalTo($deck->id()) && $game->winner()->equalTo($owner);
                    $okLoser = $game->loserDeck()->equalTo($deck->id()) && $game->loser()->equalTo($owner);

                    if (false === $okWinner && false === $okLoser) {
                        continue;
                    }

                    if (false === $game->winner()->equalTo($owner) && false === $game->loser()->equalTo($owner)) {
                        continue;
                    }
                }

                if (false === $game->winnerDeck()->equalTo($deck->id())
                    && false === $game->loserDeck()->equalTo($deck->id())) {
                    continue;
                }

                if ($game->winnerDeck()->equalTo($deck->id())) {
                    $deckWins++;
                }

                if ($game->loserDeck()->equalTo($deck->id())) {
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
