<?php declare(strict_types=1);

namespace AdnanMula\Cards\Entrypoint\Controller\Keyforge\Game\List;

use AdnanMula\Cards\Application\Query\Keyforge\Game\GetGamesQuery;
use AdnanMula\Cards\Entrypoint\Controller\Shared\Controller;
use AdnanMula\Criteria\Criteria;
use AdnanMula\Criteria\Filter\Filter;
use AdnanMula\Criteria\Filter\Filters;
use AdnanMula\Criteria\Filter\FilterType;
use AdnanMula\Criteria\FilterField\FilterField;
use AdnanMula\Criteria\FilterValue\FilterOperator;
use AdnanMula\Criteria\FilterValue\StringFilterValue;
use AdnanMula\Criteria\Sorting\Order;
use AdnanMula\Criteria\Sorting\OrderType;
use AdnanMula\Criteria\Sorting\Sorting;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

final class GetGamesController extends Controller
{
    public function __invoke(Request $request): JsonResponse
    {
        $this->assertIsLogged();

        $result = $this->extractResult(
            $this->bus->dispatch(new GetGamesQuery($this->getSearch($request))),
        );

        $response = [
            'recordsTotal' => $result['total'],
            'recordsFiltered' => $result['totalFiltered'],
            'data' => $result['games'],
            'draw' => (int) $request->get('draw'),
        ];

        return new JsonResponse($response);
    }

    private function getSearch(Request $request): Criteria
    {
        $deckId = $request->get('deckId');
        $userId = $request->get('userId');

        $filters = [];

        if (null !== $deckId && null === $userId) {
            $filters[] = new Filters(
                FilterType::AND,
                FilterType::OR,
                new Filter(new FilterField('winner_deck'), new StringFilterValue($deckId), FilterOperator::EQUAL),
                new Filter(new FilterField('loser_deck'), new StringFilterValue($deckId), FilterOperator::EQUAL),
            );
        }

        if (null !== $userId && null === $deckId) {
            $filters[] = new Filters(
                FilterType::AND,
                FilterType::OR,
                new Filter(new FilterField('winner'), new StringFilterValue($userId), FilterOperator::EQUAL),
                new Filter(new FilterField('loser'), new StringFilterValue($userId), FilterOperator::EQUAL),
            );
        }

        if (null !== $userId && null !== $deckId) {
            $filters[] = new Filters(
                FilterType::OR,
                FilterType::AND,
                new Filter(new FilterField('winner'), new StringFilterValue($userId), FilterOperator::EQUAL),
                new Filter(new FilterField('winner_deck'), new StringFilterValue($deckId), FilterOperator::EQUAL),
            );

            $filters[] = new Filters(
                FilterType::OR,
                FilterType::AND,
                new Filter(new FilterField('loser'), new StringFilterValue($userId), FilterOperator::EQUAL),
                new Filter(new FilterField('loser_deck'), new StringFilterValue($deckId), FilterOperator::EQUAL),
            );
        }

        $start = $request->get('start');
        $length = $request->get('length');

        $offset = null;
        $limit = null;

        if (null !== $start && null !== $length) {
            $offset = (int) $request->get('start');
            $limit = (int) $request->get('length');
        }

        return new Criteria(
            $this->getOrder($request),
            $offset,
            $limit,
            ...$filters,
        );
    }

    private function getOrder(Request $request): ?Sorting
    {
        $queryOrder = $request->get('order');

        $order = null;

        if (\count($queryOrder) > 0) {
            $orderColumns = [
                6 => 'date',
            ];

            $orderField = $orderColumns[(int)$queryOrder[0]['column']] ?? null;
            $orderType = $queryOrder[0]['dir'] ?? null;

            if (null !== $orderField && null !== $orderType) {
                $orderBy = new Order(new FilterField($orderField), OrderType::from($orderType));

                if ($orderBy->field()->value() === 'date') {
                    return new Sorting($orderBy, new Order(new FilterField('created_at'), $orderBy->type()));
                }

                $order = new Sorting($orderBy);
            }
        }

        return $order;
    }
}