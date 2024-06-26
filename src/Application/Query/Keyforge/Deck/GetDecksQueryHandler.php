<?php declare(strict_types=1);

namespace AdnanMula\Cards\Application\Query\Keyforge\Deck;

use AdnanMula\Cards\Domain\Model\Keyforge\Deck\KeyforgeDeckRepository;
use AdnanMula\Cards\Domain\Model\Shared\UserRepository;
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

final class GetDecksQueryHandler
{
    public function __construct(
        private KeyforgeDeckRepository $repository,
        private UserRepository $userRepository,
    ) {}

    public function __invoke(GetDecksQuery $query): array
    {
        if (null !== $query->deckId) {
            $deck = $this->repository->searchOne(
                new Criteria(
                    null,
                    null,
                    null,
                    new AndFilterGroup(
                        FilterType::AND,
                        new Filter(new FilterField('id'), new StringFilterValue($query->deckId->value()), FilterOperator::EQUAL),
                    ),
                ),
            );

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

        if (\count($query->owners) > 0) {
            foreach ($query->owners as $owner) {
                $expressions[] = new Filter(new FilterField('owner'), new StringFilterValue($owner), FilterOperator::EQUAL);
            }
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

        $countCriteria = new Criteria(
            null,
            null,
            null,
            ...$criteria->filterGroups(),
        );

        $decks = $this->repository->search($criteria);
        $totalFiltered = $this->repository->count($countCriteria);
        $total = $this->repository->count(new Criteria(null, null, null));

        return [
            'decks' => $decks,
            'total' => $total,
            'totalFiltered' => $totalFiltered,
            'start' => $query->start,
            'length' => $query->length,
        ];
    }
}
