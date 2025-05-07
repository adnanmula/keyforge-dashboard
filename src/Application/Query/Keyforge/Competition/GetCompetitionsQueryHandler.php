<?php declare(strict_types=1);

namespace AdnanMula\Cards\Application\Query\Keyforge\Competition;

use AdnanMula\Cards\Domain\Model\Keyforge\Game\KeyforgeCompetitionRepository;
use AdnanMula\Cards\Domain\Model\Shared\User;
use AdnanMula\Cards\Domain\Model\Shared\ValueObject\CompetitionVisibility;
use AdnanMula\Criteria\Criteria;
use AdnanMula\Criteria\Filter\Filter;
use AdnanMula\Criteria\Filter\FilterType;
use AdnanMula\Criteria\FilterField\FilterField;
use AdnanMula\Criteria\FilterGroup\AndFilterGroup;
use AdnanMula\Criteria\FilterValue\ArrayElementFilterValue;
use AdnanMula\Criteria\FilterValue\FilterOperator;
use AdnanMula\Criteria\FilterValue\StringFilterValue;
use AdnanMula\Criteria\Sorting\Order;
use AdnanMula\Criteria\Sorting\OrderType;
use AdnanMula\Criteria\Sorting\Sorting;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Finder\Exception\AccessDeniedException;

final readonly class GetCompetitionsQueryHandler
{
    public function __construct(
        private KeyforgeCompetitionRepository $repository,
        private Security $security,
    ) {}

    public function __invoke(GetCompetitionsQuery $query): array
    {
        /** @var ?User $user */
        $user = $this->security->getUser();

        if (null === $user) {
            throw new AccessDeniedException();
        }

        $criteria = new Criteria(
            $query->start,
            $query->length,
            new Sorting(
                new Order(
                    new FilterField('created_at'),
                    OrderType::DESC,
                ),
            ),
            new AndFilterGroup(
                FilterType::OR,
                new Filter(
                    new FilterField('visibility'),
                    new StringFilterValue(CompetitionVisibility::PUBLIC->value),
                    FilterOperator::EQUAL,
                ),
                new Filter(
                    new FilterField('admins'),
                    new ArrayElementFilterValue($user->id()->value()),
                    FilterOperator::IN_ARRAY,
                ),
                new Filter(
                    new FilterField('players'),
                    new ArrayElementFilterValue($user->id()->value()),
                    FilterOperator::IN_ARRAY,
                ),
            ),
        );

        $competitions = $this->repository->search($criteria);
        $total = $this->repository->count($criteria->withoutPaginationAndSorting());

        return [
            'competitions' => $competitions,
            'total' => $total,
            'totalFiltered' => $total,
            'start' => $query->start,
            'length' => $query->length,
        ];
    }
}
