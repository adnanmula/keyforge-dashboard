<?php declare(strict_types=1);

namespace AdnanMula\Cards\Application\Query\Keyforge\Deck;

use AdnanMula\Cards\Domain\Model\Keyforge\Deck\KeyforgeDeckRepository;
use AdnanMula\Cards\Domain\Model\Shared\UserRepository;
use AdnanMula\Criteria\Criteria;
use AdnanMula\Criteria\Filter\Filter;
use AdnanMula\Criteria\Filter\FilterType;
use AdnanMula\Criteria\FilterField\FilterField;
use AdnanMula\Criteria\FilterGroup\AndFilterGroup;
use AdnanMula\Criteria\FilterValue\FilterOperator;
use AdnanMula\Criteria\FilterValue\StringArrayFilterValue;
use AdnanMula\Criteria\FilterValue\StringFilterValue;

final readonly class GetDecksQueryHandler
{
    public function __construct(
        private KeyforgeDeckRepository $repository,
        private UserRepository $userRepository,
    ) {}

    public function __invoke(GetDecksQuery $query): array
    {
        $criteria = $query->criteria;
        $isMyDecks = null !== $query->owner;

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
                return ['decks' => [], 'total' => 0, 'totalFiltered' => 0, 'start' => $criteria->offset(), 'length' => $criteria->limit()];
            }

            return [
                'decks' => [$deck],
                'total' => 1,
                'totalFiltered' => 1,
                'start' => $criteria->offset(),
                'length' => $criteria->limit(),
            ];
        }

        if (null !== $query->onlyFriends) {
            $friends = \array_map(
                static fn (array $u) => $u['id'],
                $this->userRepository->friends($query->onlyFriends),
            );

            $criteria = $criteria->with(
                new AndFilterGroup(
                    FilterType::AND,
                    new Filter(
                        new FilterField('owner'),
                        new StringArrayFilterValue($query->onlyFriends->value(), ...\array_unique($friends)),
                        FilterOperator::IN,
                    ),
                ),
            );
        }

        $decks = $this->repository->search($criteria, $isMyDecks);
        $totalFiltered = $this->repository->count($criteria->withoutPaginationAndSorting());
        $total = $this->repository->count(new Criteria(null, null, null));

        return [
            'decks' => $decks,
            'total' => $total,
            'totalFiltered' => $totalFiltered,
            'start' => $criteria->offset(),
            'length' => $criteria->limit(),
        ];
    }
}
