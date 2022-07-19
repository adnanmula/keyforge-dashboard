<?php declare(strict_types=1);

namespace AdnanMula\Cards\Entrypoint\Controller\Keyforge\Stats\Deck;

use AdnanMula\Cards\Application\Query\Keyforge\Deck\GetDecksQuery;
use AdnanMula\Cards\Domain\Model\Shared\QueryOrder;
use AdnanMula\Cards\Entrypoint\Controller\Shared\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

final class GetDecksController extends Controller
{
    public function __invoke(Request $request): JsonResponse
    {
        $orderColumns = [
            0 => 'name',
            1 => 'set',
            3 => 'win_rate',
            4 => 'sas',
        ];

        $order = null;
        $queryOrder = $request->get('order');

        if (\count($queryOrder) > 0) {
            $orderField = $orderColumns[(int) $queryOrder[0]['column']] ?? null;
            $orderType = $queryOrder[0]['dir'] ?? null;

            if (null !== $orderField && null !== $orderType) {
                $order = new QueryOrder(field: $orderField, order: $orderType);
            }
        }

        $decks = $this->extractResult(
            $this->bus->dispatch(new GetDecksQuery($request->get('start'), $request->get('length'), $order)),
        );

        $response = [
            'recordsTotal' => $decks['total'],
            'recordsFiltered' => $decks['total'],
            'data' => $decks['decks'],
        ];

        return new JsonResponse($response);
    }
}
