<?php declare(strict_types=1);

namespace AdnanMula\Cards\Application\Query\Keyforge\Deck;

use AdnanMula\Cards\Domain\Model\Keyforge\Deck\ValueObject\KeyforgeDeckType;
use AdnanMula\Cards\Domain\Model\Keyforge\Deck\ValueObject\KeyforgeHouse;
use AdnanMula\Cards\Domain\Model\Keyforge\Deck\ValueObject\KeyforgeSet;
use AdnanMula\Cards\Domain\Model\Shared\ValueObject\Uuid;
use AdnanMula\Cards\Shared\CriteriaQuery;
use AdnanMula\Criteria\Criteria;
use AdnanMula\Criteria\Filter\Filter;
use AdnanMula\Criteria\Filter\FilterType;
use AdnanMula\Criteria\FilterField\FilterField;
use AdnanMula\Criteria\FilterGroup\AndFilterGroup;
use AdnanMula\Criteria\FilterGroup\FilterGroup;
use AdnanMula\Criteria\FilterValue\ArrayElementFilterValue;
use AdnanMula\Criteria\FilterValue\FilterOperator;
use AdnanMula\Criteria\FilterValue\IntFilterValue;
use AdnanMula\Criteria\FilterValue\NullFilterValue;
use AdnanMula\Criteria\FilterValue\StringArrayFilterValue;
use AdnanMula\Criteria\FilterValue\StringFilterValue;
use AdnanMula\Criteria\Sorting\Order;
use AdnanMula\Criteria\Sorting\OrderType;
use AdnanMula\Criteria\Sorting\Sorting;
use Assert\Assert;

final readonly class GetDecksQuery extends CriteriaQuery
{
    private(set) ?Uuid $deckId;
    private(set) ?string $owner;
    private(set) ?Uuid $onlyFriends;

    public function __construct(
        $start = null,
        $length = null,
        ?string $deck = null,
        ?string $orderField = null,
        ?string $orderDirection = null,
        ?array $sets = null,
        ?string $houseFilterType = null,
        ?array $houses = null,
        ?array $housesExcluded = null,
        ?array $deckTypes = null,
        ?string $deckId = null,
        ?string $owner = null,
        array $owners = [],
        bool $onlyOwned = false,
        ?string $tagFilterType = null,
        array $tags = [],
        array $tagsExcluded = [],
        $maxSas = 150,
        $minSas = 0,
        $onlyFriends = null,
        ?string $tagPrivateFilterType = null,
        array $tagsPrivate = [],
        array $tagsPrivateExcluded = [],
        array $deckIds = [],
    ) {
        Assert::lazy()
            ->that($start, 'start')->nullOr()->integerish()->greaterOrEqualThan(0)
            ->that($length, 'length')->nullOr()->integerish()->greaterThan(0)
            ->that($deck, 'deck')->nullOr()->string()->notBlank()
            ->that($orderField, 'orderField')->nullOr()->string()->notBlank()
            ->that($orderDirection, 'orderDirection')->nullOr()->inArray([OrderType::ASC->value, OrderType::DESC->value])
            ->that($sets, 'set')->nullOr()->all()->inArray(KeyforgeSet::values())
            ->that($houseFilterType, 'houseFilterType')->nullOr()->inArray(['all', 'any'])
            ->that($houses, 'house')->nullOr()->all()->inArray(KeyforgeHouse::values())
            ->that($housesExcluded, 'houseExcluded')->nullOr()->all()->inArray(KeyforgeHouse::values())
            ->that($deckTypes, 'deckTypes')->nullOr()->all()->inArray(KeyforgeDeckType::values())
            ->that($deckId, 'deckId')->nullOr()->uuid()
            ->that($owner, 'owner')->nullOr()->uuid()
            ->that($owners, 'owners')->all()->uuid()
            ->that($onlyOwned, 'onlyOwned')->boolean()
            ->that($tagFilterType, 'tagFilterType')->nullOr()->inArray(['all', 'any'])
            ->that($tags, 'tags')->all()->uuid()
            ->that($tagsExcluded, 'tagsExcluded')->all()->uuid()
            ->that($maxSas, 'maxSas')->integerish()
            ->that($minSas, 'minSas')->integerish()
            ->that($onlyFriends, 'onlyFriends')->nullOr()->uuid()
            ->that($tagPrivateFilterType, 'tagPrivateFilterType')->nullOr()->inArray(['all', 'any'])
            ->that($tagsPrivate, 'tagsPrivate')->all()->uuid()
            ->that($tagsPrivateExcluded, 'tagsPrivateExcluded')->all()->uuid()
            ->that($deckIds, 'deckIds')->all()->uuid()
            ->verifyNow();

        $this->deckId = null !== $deckId ? Uuid::from($deckId) : null;
        $this->owner = $owner;
        $this->onlyFriends = null === $onlyFriends ? null : Uuid::from($onlyFriends);

        $filters = [];

        $filters[] = $this->miscFilter($owner, $deck, $onlyOwned, (int) $minSas, (int) $maxSas);
        $filters[] = $this->tagsIncludedFilter($tagFilterType, ...$tags);
        $filters[] = $this->tagsExcludedFilter($tagFilterType, ...$tagsExcluded);
        $filters[] = $this->tagsPrivateIncludedFilter($tagPrivateFilterType, ...$tagsPrivate);
        $filters[] = $this->tagsPrivateExcludedFilter($tagPrivateFilterType, ...$tagsPrivateExcluded);
        $filters[] = $this->housesFilter($houseFilterType, ...$houses ?? []);
        $filters[] = $this->housesExcludedFilter(...$housesExcluded ?? []);
        $filters[] = $this->deckTypeFilter(...$deckTypes ?? []);
        $filters[] = $this->ownersFilter(...$owners);
        $filters[] = $this->setsFilter(...$sets ?? []);
        $filters[] = $this->deckIdsFilters(...$deckIds);

        $criteria = new Criteria(
            null === $start ? null : (int) $start,
            null === $length ? null : (int) $length,
            $this->sorting(
                $orderField,
                $orderDirection,
                $owner,
            ),
            ...\array_filter($filters),
        );

        parent::__construct($criteria);
    }

    private function miscFilter(?string $owner, ?string $deck, bool $onlyOwned, int $minSas, int $maxSas): FilterGroup
    {
        $expressions = [];

        if (null !== $owner) {
            $expressions[] = new Filter(new FilterField('owner'), new StringFilterValue($owner), FilterOperator::EQUAL);
        }

        if (null !== $deck) {
            $expressions[] = new Filter(new FilterField('name'), new StringFilterValue($deck), FilterOperator::CONTAINS_INSENSITIVE);
        }

        if (true === $onlyOwned) {
            $expressions[] = new Filter(new FilterField('owner'), new NullFilterValue(), FilterOperator::IS_NOT_NULL);
        }

        $expressions[] = new Filter(new FilterField('sas'), new IntFilterValue($maxSas), FilterOperator::LESS_OR_EQUAL);
        $expressions[] = new Filter(new FilterField('sas'), new IntFilterValue($minSas), FilterOperator::GREATER_OR_EQUAL);

        return new AndFilterGroup(FilterType::AND, ...$expressions);
    }

    private function tagsIncludedFilter(?string $filterType, string ...$tags): ?FilterGroup
    {
        if (null !== $filterType && \count($tags) > 0) {
            $tagsExpressions = [];

            foreach ($tags as $tag) {
                $tagsExpressions[] = new Filter(new FilterField('tags'), new ArrayElementFilterValue($tag), FilterOperator::IN_ARRAY);
            }

            return new AndFilterGroup(
                $filterType === 'any' ? FilterType::OR : FilterType::AND,
                ...$tagsExpressions,
            );
        }

        return null;
    }

    private function tagsExcludedFilter(?string $filterType, string ...$tags): ?FilterGroup
    {
        if (null !== $filterType && \count($tags) > 0) {
            $tagsExpressions = [];

            foreach ($tags as $tag) {
                $tagsExpressions[] = new Filter(new FilterField('tags'), new ArrayElementFilterValue($tag), FilterOperator::NOT_IN_ARRAY);
            }

            return new AndFilterGroup(
                $filterType === 'any' ? FilterType::OR : FilterType::AND,
                ...$tagsExpressions,
            );
        }

        return null;
    }

    private function tagsPrivateIncludedFilter(?string $filterType, string ...$tags): ?FilterGroup
    {
        if (null !== $filterType && \count($tags) > 0) {
            $tagsExpressions = [];

            foreach ($tags as $tag) {
                $tagsExpressions[] = new Filter(new FilterField('user_tags'), new ArrayElementFilterValue($tag), FilterOperator::IN_ARRAY);
            }

            return new AndFilterGroup(
                $filterType === 'any' ? FilterType::OR : FilterType::AND,
                ...$tagsExpressions,
            );
        }

        return null;
    }

    private function tagsPrivateExcludedFilter(?string $filterType, string ...$tags): ?FilterGroup
    {
        if (null !== $filterType && \count($tags) > 0) {
            $tagsExpressions = [];

            foreach ($tags as $tag) {
                $tagsExpressions[] = new Filter(new FilterField('user_tags'), new ArrayElementFilterValue($tag), FilterOperator::NOT_IN_ARRAY);
            }

            return new AndFilterGroup(
                $filterType === 'any' ? FilterType::OR : FilterType::AND,
                ...$tagsExpressions,
            );
        }

        return null;
    }

    private function housesFilter(?string $filterType, string ...$houses): ?FilterGroup
    {
        if (null !== $filterType && \count($houses) > 0) {
            return new AndFilterGroup(
                $filterType === 'any' ? FilterType::OR : FilterType::AND,
                ...\array_map(
                    static fn (string $house): Filter => new Filter(new FilterField('houses'), new ArrayElementFilterValue($house), FilterOperator::IN_ARRAY),
                    $houses,
                ),
            );
        }

        return null;
    }

    private function housesExcludedFilter(string ...$houses): ?FilterGroup
    {
        if (\count($houses) > 0) {
            return new AndFilterGroup(
                FilterType::AND,
                ...\array_map(
                    static fn (string $house): Filter => new Filter(new FilterField('houses'), new ArrayElementFilterValue($house), FilterOperator::NOT_IN_ARRAY),
                    $houses,
                ),
            );
        }

        return null;
    }

    private function deckTypeFilter(string ...$types): ?FilterGroup
    {
        if (\count($types) > 0) {
            return new AndFilterGroup(
                FilterType::OR,
                ...\array_map(
                    static fn (string $type): Filter => new Filter(new FilterField('deck_type'), new StringArrayFilterValue($type), FilterOperator::IN),
                    $types,
                ),
            );
        }

        return null;
    }

    private function ownersFilter(string ...$owners): ?FilterGroup
    {
        if (\count($owners) > 0) {
            return new AndFilterGroup(
                FilterType::OR,
                ...\array_map(
                    static fn (string $owner): Filter => new Filter(new FilterField('owner'), new StringFilterValue($owner), FilterOperator::EQUAL),
                    \array_unique($owners),
                ),
            );
        }

        return null;
    }

    private function setsFilter(string ...$sets): ?FilterGroup
    {
        if (\count($sets) > 0) {
            $setFilterExpressions = [];

            foreach ($sets as $set) {
                $setFilterExpressions[] = new Filter(new FilterField('set'), new StringFilterValue($set), FilterOperator::EQUAL);
            }

            return new AndFilterGroup(FilterType::OR, ...$setFilterExpressions);
        }

        return null;
    }

    private function deckIdsFilters(string ...$deckIds): ?FilterGroup
    {
        if (\count($deckIds) > 0) {
            return new AndFilterGroup(
                FilterType::AND,
                new Filter(new FilterField('id'), new StringArrayFilterValue(...$deckIds), FilterOperator::IN),
            );
        }

        return null;
    }

    private function sorting(?string $orderField, ?string $orderDirection, ?string $owner): ?Sorting
    {
        if (null !== $orderField && null !== $orderDirection) {
            if ($orderField === 'win_rate') {
                if (null === $owner) {
                    return new Sorting(
                        new Order(new FilterField('wins_vs_users'), OrderType::from($orderDirection)),
                        new Order(new FilterField('losses_vs_users'), OrderType::from($orderDirection) === OrderType::ASC ? OrderType::DESC : OrderType::ASC),
                        new Order(new FilterField('id'), OrderType::ASC),
                    );
                }

                return new Sorting(
                    new Order(new FilterField('wins'), OrderType::from($orderDirection)),
                    new Order(new FilterField('losses'), OrderType::from($orderDirection) === OrderType::ASC ? OrderType::DESC : OrderType::ASC),
                    new Order(new FilterField('id'), OrderType::ASC),
                );
            }

            return new Sorting(
                new Order(new FilterField($orderField), OrderType::from($orderDirection)),
                new Order(new FilterField('id'), OrderType::ASC),
            );
        }

        return null;
    }
}
