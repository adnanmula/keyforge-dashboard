<?php declare(strict_types=1);

namespace AdnanMula\Cards\Entrypoint\Controller\Keyforge\Stats\Deck;

use AdnanMula\Cards\Application\Query\Keyforge\Deck\GetDecksQuery;
use AdnanMula\Cards\Entrypoint\Controller\Shared\Controller;
use AdnanMula\Cards\Infrastructure\Criteria\FilterField\FilterField;
use AdnanMula\Cards\Infrastructure\Criteria\Sorting\Order;
use AdnanMula\Cards\Infrastructure\Criteria\Sorting\OrderType;
use AdnanMula\Cards\Infrastructure\Criteria\Sorting\Sorting;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

final class GetDecksController extends Controller
{
    public function __invoke(Request $request): JsonResponse
    {
        $sorting = null;
        $queryOrder = $request->get('order');

        $searchDeck = null;

        if (null !== $request->get('search') && '' !== $request->get('search')['value']) {
            $searchDeck = $request->get('search')['value'];
        }

        $columnFilters = $request->get('columns', []);
        $searchHouse = null;
        $searchSet = null;
        $searchOwner = null;

        if (\array_key_exists(1, $columnFilters)) {
            $value = $columnFilters[1]['search']['value'];

            if ('' !== $value) {
                $searchSet = $value;
            }
        }

        if (\array_key_exists(2, $columnFilters)) {
            $value = $columnFilters[2]['search']['value'];

            if ('' !== $value) {
                $searchHouse = $value;
            }
        }

        if (\array_key_exists(6, $columnFilters)) {
            $value = $columnFilters[6]['search']['value'];

            if ('' !== $value) {
                $searchOwner = $value;
            }
        }

        if (\count($queryOrder) > 0) {
            $orderColumns = [
                0 => 'name',
                1 => 'set',
                3 => 'win_rate',
                4 => 'sas',
            ];

            $orderField = $orderColumns[(int) $queryOrder[0]['column']] ?? null;
            $orderType = $queryOrder[0]['dir'] ?? null;

            if (null !== $orderField && null !== $orderType) {
                if ($orderField === 'win_rate') {
                    $sorting = new Sorting(
                        new Order(new FilterField('wins'), OrderType::from($orderType)),
                        new Order(new FilterField('losses'), OrderType::from($orderType) === OrderType::ASC ? OrderType::DESC : OrderType::ASC),
                    );
                } else {
                    $sorting = new Sorting(new Order(new FilterField($orderField), OrderType::from($orderType)));
                }
            }
        }

        $decks = $this->extractResult(
            $this->bus->dispatch(new GetDecksQuery(
                $request->get('start'),
                $request->get('length'),
                $searchDeck,
                $searchSet,
                $searchHouse,
                $sorting,
                null,
                $searchOwner,
                true,
            )),
        );

        $response = [
            'recordsTotal' => $decks['total'],
            'recordsFiltered' => $decks['totalFiltered'],
            'data' => $decks['decks'],
        ];

        return new JsonResponse($response);
    }
}
