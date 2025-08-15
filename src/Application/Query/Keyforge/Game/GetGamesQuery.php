<?php declare(strict_types=1);

namespace AdnanMula\Cards\Application\Query\Keyforge\Game;

use AdnanMula\Cards\Domain\Model\Keyforge\Game\ValueObject\KeyforgeCompetition;
use AdnanMula\Cards\Shared\CriteriaQuery;
use AdnanMula\Criteria\Criteria;
use AdnanMula\Criteria\Filter\CompositeFilter;
use AdnanMula\Criteria\Filter\Filter;
use AdnanMula\Criteria\Filter\FilterOperator;
use AdnanMula\Criteria\Filter\Filters;
use AdnanMula\Criteria\Filter\FilterType;
use AdnanMula\Criteria\FilterField\FilterField;
use AdnanMula\Criteria\FilterField\JsonKeyFilterField;
use AdnanMula\Criteria\FilterValue\IntFilterValue;
use AdnanMula\Criteria\FilterValue\StringArrayFilterValue;
use AdnanMula\Criteria\FilterValue\StringFilterValue;
use AdnanMula\Criteria\Sorting\Order;
use AdnanMula\Criteria\Sorting\OrderType;
use AdnanMula\Criteria\Sorting\Sorting;
use Assert\Assert;

final readonly class GetGamesQuery extends CriteriaQuery
{
    public function __construct(
        $ids = null,
        $deckId = null,
        $userId = null,
        $winners = null,
        $losers = null,
        $loserScores = null,
        $competitions = null,
        $approved = null,
        $start = null,
        $length = null,
        $orderField = null,
        $orderDirection = null,
    ) {
        Assert::lazy()
            ->that($ids, 'ids')->nullOr()->all()->uuid()
            ->that($deckId, 'deckId')->nullOr()->uuid()
            ->that($userId, 'userId')->nullOr()->uuid()
            ->that($winners, 'winners')->nullOr()->all()->uuid()
            ->that($losers, 'losers')->nullOr()->all()->uuid()
            ->that($loserScores, 'loserScores')->nullOr()->all()->integerish()->between(0, 2)
            ->that($competitions, 'competitions')->nullOr()->all()->inArray(KeyforgeCompetition::values())
            ->that($approved, 'approved')->nullOr()->boolean()
            ->that($start, 'start')->nullOr()->integerish()->min(0)
            ->that($length, 'length')->nullOr()->integerish()->min(0)
            ->that($orderField, 'orderField')->nullOr()->string()->notBlank()
            ->that($orderDirection, 'orderDirection')->nullOr()->inArray([OrderType::ASC->value, OrderType::DESC->value])
            ->verifyNow();

        $filters = [];
        $filters[] = $this->idFilter(...$ids ?? []);
        $filters[] = $this->deckFilter($userId, $deckId);
        $filters[] = $this->winnerFilter(...$winners ?? []);
        $filters[] = $this->loserFilter(...$losers ?? []);
        $filters[] = $this->scoreFilter(...$loserScores ?? []);
        $filters[] = $this->competitionFilter(...$competitions ?? []);
        $filters[] = $this->approvedFilter($approved);

        $criteria = new Criteria(
            new Filters(FilterType::AND, ...\array_filter($filters)),
            null === $start ? null : (int) $start,
            null === $length ? null : (int) $length,
            $this->sorting($orderField, $orderDirection),
        );

        parent::__construct($criteria);
    }

    private function idFilter(string ...$ids): ?Filter
    {
        if (0 === count($ids)) {
            return null;
        }

        return new Filter(new FilterField('id'), new StringArrayFilterValue(...$ids), FilterOperator::IN);
    }

    private function deckFilter(?string $userId, ?string $deckId): ?CompositeFilter
    {
        if (null !== $deckId && null === $userId) {
            return new CompositeFilter(
                FilterType::OR,
                new Filter(new FilterField('winner_deck'), new StringFilterValue($deckId), FilterOperator::EQUAL),
                new Filter(new FilterField('loser_deck'), new StringFilterValue($deckId), FilterOperator::EQUAL),
            );
        }

        if (null !== $userId && null === $deckId) {
            return new CompositeFilter(
                FilterType::OR,
                new Filter(new FilterField('winner'), new StringFilterValue($userId), FilterOperator::EQUAL),
                new Filter(new FilterField('loser'), new StringFilterValue($userId), FilterOperator::EQUAL),
            );
        }

        if (null !== $userId && null !== $deckId) {
            return new CompositeFilter(
                FilterType::OR,
                new Filter(new FilterField('winner'), new StringFilterValue($userId), FilterOperator::EQUAL),
                new Filter(new FilterField('winner_deck'), new StringFilterValue($deckId), FilterOperator::EQUAL),
                new Filter(new FilterField('loser'), new StringFilterValue($userId), FilterOperator::EQUAL),
                new Filter(new FilterField('loser_deck'), new StringFilterValue($deckId), FilterOperator::EQUAL),
            );
        }

        return null;
    }

    private function winnerFilter(string ...$winners): ?Filter
    {
        if (\count($winners) > 0) {
            return new Filter(new FilterField('winner'), new StringArrayFilterValue(...$winners), FilterOperator::IN);
        }

        return null;
    }

    private function loserFilter(string ...$losers): ?Filter
    {
        if (\count($losers) > 0) {
            return new Filter(new FilterField('loser'), new StringArrayFilterValue(...$losers), FilterOperator::IN);
        }

        return null;
    }

    private function scoreFilter(string ...$scores): ?CompositeFilter
    {
        if (\count($scores) > 0) {
            return new CompositeFilter(
                FilterType::AND,
                new Filter(
                    new JsonKeyFilterField('score', 'loser_score'),
                    new StringArrayFilterValue(...$scores),
                    FilterOperator::IN,
                ),
            );
        }

        return null;
    }

    private function competitionFilter(string ...$competitions): ?Filter
    {
        if (\count($competitions) > 0) {
            return new Filter(new FilterField('competition'), new StringArrayFilterValue(...$competitions), FilterOperator::IN);
        }

        return null;
    }

    private function approvedFilter(?bool $approved): ?Filter
    {
        if (null === $approved) {
            return null;
        }

        if (true === $approved) {
            return new Filter(new FilterField('approved'), new IntFilterValue(1), FilterOperator::EQUAL);
        }

        return new Filter(new FilterField('approved'), new IntFilterValue(0), FilterOperator::EQUAL);
    }

    private function sorting(?string $field, ?string $dir): ?Sorting
    {
        if (null !== $field && null !== $dir) {
            $orderBy = new Order(new FilterField($field), OrderType::from($dir));

            if ($field === 'date') {
                return new Sorting($orderBy, new Order(new FilterField('created_at'), $orderBy->type()));
            }

            return new Sorting($orderBy);
        }

        return null;
    }
}
