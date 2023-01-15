<?php declare(strict_types=1);

namespace AdnanMula\Cards\Entrypoint\Controller\Keyforge\Stats\Deck;

use AdnanMula\Cards\Application\Query\Keyforge\Deck\GetDecksQuery;
use AdnanMula\Cards\Entrypoint\Controller\Shared\Controller;
use AdnanMula\Criteria\FilterField\FilterField;
use AdnanMula\Criteria\Sorting\Order;
use AdnanMula\Criteria\Sorting\OrderType;
use AdnanMula\Criteria\Sorting\Sorting;
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

        if (null !== $queryOrder && \count($queryOrder) > 0) {
            $orderColumns = [
                1 => 'name',
                2 => 'set',
                4 => 'win_rate',
                5 => 'sas',
            ];

            $orderField = $orderColumns[(int) $queryOrder[0]['column']] ?? null;
            $orderType = $queryOrder[0]['dir'] ?? null;

            if (null !== $orderField && null !== $orderType) {
                if ($orderField === 'win_rate') {
                    $sorting = new Sorting(
                        new Order(new FilterField('wins'), OrderType::from($orderType)),
                        new Order(new FilterField('losses'), OrderType::from($orderType) === OrderType::ASC ? OrderType::DESC : OrderType::ASC),
                        new Order(new FilterField('id'), OrderType::ASC),
                    );
                } else {
                    $sorting = new Sorting(
                        new Order(new FilterField($orderField), OrderType::from($orderType)),
                        new Order(new FilterField('id'), OrderType::ASC),
                    );
                }
            }
        }

        $decks = $this->extractResult(
            $this->bus->dispatch(new GetDecksQuery(
                $request->get('start'),
                $request->get('length'),
                $searchDeck,
                $request->query->all()['extraFilterSet'] ?? null,
                $request->query->get('extraFilterHouseFilterType'),
                $request->query->all()['extraFilterHouses'] ?? null,
                $sorting,
                $request->get('extraDeckId'),
                $request->get('extraFilterOwner'),
                $request->get('extraFilterOnlyOwned') === 'true',
                $request->get('extraFilterTagType'),
                $request->query->all()['extraFilterTags'] ?? [],
                $request->query->all()['extraFilterTagsExcluded'] ?? [],
                $request->get('extraFilterMaxSas', 150),
                $request->get('extraFilterMinSas', 0),
            )),
        );

        $response = [
            'recordsTotal' => $decks['total'],
            'recordsFiltered' => $decks['totalFiltered'],
            'data' => $decks['decks'],
            'draw' => (int) $request->get('draw'),
        ];

        return new JsonResponse($response);
    }
}
