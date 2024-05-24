<?php declare(strict_types=1);

namespace AdnanMula\Cards\Application\Query\Keyforge\Deck;

use AdnanMula\Cards\Domain\Model\Keyforge\Deck\KeyforgeDeck;
use AdnanMula\Cards\Domain\Model\Keyforge\Deck\KeyforgeDeckRepository;
use AdnanMula\Cards\Domain\Model\Keyforge\Deck\ValueObject\KeyforgeDeckUserData;
use AdnanMula\Cards\Domain\Model\Keyforge\Game\KeyforgeGame;
use AdnanMula\Cards\Domain\Model\Keyforge\Game\KeyforgeGameRepository;
use AdnanMula\Cards\Domain\Model\Keyforge\User\KeyforgeUser;
use AdnanMula\Cards\Domain\Model\Keyforge\User\KeyforgeUserRepository;
use AdnanMula\Cards\Domain\Model\Shared\UserRepository;
use AdnanMula\Cards\Domain\Model\Shared\ValueObject\Uuid;
use AdnanMula\Criteria\Criteria;
use AdnanMula\Criteria\Filter\Filter;
use AdnanMula\Criteria\Filter\FilterType;
use AdnanMula\Criteria\FilterField\FilterField;
use AdnanMula\Criteria\FilterGroup\AndFilterGroup;
use AdnanMula\Criteria\FilterValue\ArrayElementFilterValue;
use AdnanMula\Criteria\FilterValue\FilterOperator;
use AdnanMula\Criteria\FilterValue\IntFilterValue;
use AdnanMula\Criteria\FilterValue\NullFilterValue;
use AdnanMula\Criteria\FilterValue\StringArrayFilterValue;
use AdnanMula\Criteria\FilterValue\StringFilterValue;
use AdnanMula\Criteria\Sorting\Order;
use AdnanMula\Criteria\Sorting\OrderType;

final class GetDecksQueryHandler
{
    public function __construct(
        private KeyforgeDeckRepository $repository,
        private KeyforgeUserRepository $keyforgeUserRepository,
        private KeyforgeGameRepository $gameRepository,
        private UserRepository $userRepository,
    ) {}

    public function __invoke(GetDecksQuery $query): array
    {
        if (null !== $query->deckId) {
            $deck = $this->repository->byId($query->deckId);

            if (null === $deck) {
                return ['decks' => [], 'total' => 0, 'totalFiltered' => 0, 'start' => $query->start, 'length' => $query->length];
            }

            return [
                'decks' => [$deck],
                'total' => 1,
                'totalFiltered' => 1,
                'start' => $query->start,
                'length' => $query->length,
            ];
        }

        $expressions = [];

        if (null !== $query->owner) {
            $expressions[] = new Filter(new FilterField('owner'), new StringFilterValue($query->owner->value()), FilterOperator::EQUAL);
        }

        if (null !== $query->deck) {
            $expressions[] = new Filter(new FilterField('name'), new StringFilterValue($query->deck), FilterOperator::CONTAINS_INSENSITIVE);
        }

        if ($query->onlyOwned) {
            $expressions[] = new Filter(new FilterField('owner'), new NullFilterValue(), FilterOperator::IS_NOT_NULL);
        }

        if (null !== $query->onlyFriends) {
            $friends = \array_map(
                static fn (array $u) => $u['id'],
                $this->userRepository->friends($query->onlyFriends),
            );

            $expressions[] = new Filter(new FilterField('owner'), new StringArrayFilterValue($query->onlyFriends->value(), ...$friends), FilterOperator::IN);
        }

        $expressions[] = new Filter(new FilterField('sas'), new IntFilterValue($query->maxSas), FilterOperator::LESS_OR_EQUAL);
        $expressions[] = new Filter(new FilterField('sas'), new IntFilterValue($query->minSas), FilterOperator::GREATER_OR_EQUAL);

        $filters = [new AndFilterGroup(FilterType::AND, ...$expressions)];

        if (\count($query->owners) > 0) {
            $ownerExpressions = [];

            foreach ($query->owners as $owner) {
                $ownerExpressions[] = new Filter(new FilterField('owner'), new StringFilterValue($owner), FilterOperator::EQUAL);
            }

            $filters[] = new AndFilterGroup(
                FilterType::OR,
                ...$ownerExpressions,
            );
        }

        if (\count($query->tags) > 0) {
            $tagsExpressions = [];

            foreach ($query->tags as $tag) {
                $tagsExpressions[] = new Filter(new FilterField('tags'), new ArrayElementFilterValue($tag->value()), FilterOperator::IN_ARRAY);
            }

            $filters[] = new AndFilterGroup(
                $query->tagFilterType === 'any' ? FilterType::OR : FilterType::AND,
                ...$tagsExpressions,
            );
        }

        if (\count($query->tagsExcluded) > 0) {
            $tagsExpressions = [];

            foreach ($query->tagsExcluded as $tag) {
                $tagsExpressions[] = new Filter(new FilterField('tags'), new ArrayElementFilterValue($tag->value()), FilterOperator::NOT_IN_ARRAY);
            }

            $filters[] = new AndFilterGroup(
                FilterType::AND,
                ...$tagsExpressions,
            );
        }

        if (null !== $query->sets) {
            $setFilterExpressions = [];

            foreach ($query->sets as $set) {
                $setFilterExpressions[] = new Filter(new FilterField('set'), new StringFilterValue($set), FilterOperator::EQUAL);
            }

            $filters[] = new AndFilterGroup(
                FilterType::OR,
                ...$setFilterExpressions,
            );
        }

        if (null !== $query->houses) {
            $houseFilterExpressions = [];

            foreach ($query->houses as $house) {
                $houseFilterExpressions[] = new Filter(new FilterField('houses'), new ArrayElementFilterValue($house), FilterOperator::IN_ARRAY);
            }

            $filters[] = new AndFilterGroup(
                $query->houseFilterType === 'any' ? FilterType::OR : FilterType::AND,
                ...$houseFilterExpressions,
            );
        }

        $criteria = new Criteria(
            $query->start,
            $query->length,
            $query->sorting,
            ...$filters,
        );

        $decks = $this->repository->search($criteria);

//      TODO ñapas

        if (null !== $query->owner) {
            $decks = $this->recalculateWins($query->owner, $query->deckId, ...$decks);

            if (null !== $query->sorting && $query->sorting->has('wins')) {
                $decks = $this->reorderDecks($query->sorting->get('wins'), ...$decks);
            }
        }

        if ($query->onlyOwned) {
            $decks = $this->removeNotOwnedStats($query->owner, ...$decks);

            if (null !== $query->sorting && $query->sorting->has('wins')) {
                $decks = $this->reorderDecks($query->sorting->get('wins'), ...$decks);
            }
        }

//      end ñapas

        $countCriteria = new Criteria(
            null,
            null,
            null,
            ...$criteria->filterGroups(),
        );

        $total = $this->repository->count(new Criteria(null, null, null));
        $totalFiltered = $this->repository->count($countCriteria);

        return [
            'decks' => $decks,
            'total' => $total,
            'totalFiltered' => $totalFiltered,
            'start' => $query->start,
            'length' => $query->length,
        ];
    }

    /** @return array<KeyforgeDeck> */
    private function recalculateWins(Uuid $userId, ?Uuid $deckId, KeyforgeDeck ...$decks): array
    {
        $filters = [new AndFilterGroup(
            FilterType::OR,
            new Filter(new FilterField('winner'), new StringFilterValue($userId->value()), FilterOperator::EQUAL),
            new Filter(new FilterField('loser'), new StringFilterValue($userId->value()), FilterOperator::EQUAL),
        )];

        if (null !== $deckId) {
            $filters[] = new AndFilterGroup(
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

            $deck->setUserData(
                KeyforgeDeckUserData::from(
                    $deck->userData()->id,
                    $deck->userData()->owner,
                    $deckWins,
                    $deckLosses,
                    $deck->userData()->notes,
                    $deck->userData()->tags,
                ),
            );
        }

        return $decks;
    }

    /** @return array<KeyforgeDeck> */
    private function removeNotOwnedStats(?Uuid $owner, KeyforgeDeck ...$decks): array
    {
        $users = $this->keyforgeUserRepository->search(new Criteria(null, null, null));
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

            $deck->setUserData(
                KeyforgeDeckUserData::from(
                    $deck->userData()->id,
                    $deck->userData()->owner,
                    $deckWins,
                    $deckLosses,
                    $deck->userData()->notes,
                ),
            );
        }

        return $decks;
    }

    /** @return array<KeyforgeDeck> */
    private function reorderDecks(Order $order, KeyforgeDeck ...$decks): array
    {
        if ($order->type() === OrderType::DESC) {
            \usort($decks, static function (KeyforgeDeck $a, KeyforgeDeck $b) {
                if ($a->userData()->wins === $b->userData()->wins) {
                    return $a->userData()->losses <=> $b->userData()->losses;
                }

                return $b->userData()->wins <=> $a->userData()->wins;
            });
        }

        if ($order->type() === OrderType::ASC) {
            \usort($decks, static function (KeyforgeDeck $a, KeyforgeDeck $b) {
                if ($b->userData()->wins === $a->userData()->wins) {
                    return $b->userData()->losses <=> $a->userData()->losses;
                }

                return $a->userData()->wins <=> $b->userData()->wins;
            });
        }

        return $decks;
    }
}
