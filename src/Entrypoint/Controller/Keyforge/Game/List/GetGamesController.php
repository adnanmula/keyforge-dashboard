<?php declare(strict_types=1);

namespace AdnanMula\Cards\Entrypoint\Controller\Keyforge\Game\List;

use AdnanMula\Cards\Application\Query\Keyforge\Game\GetGamesQuery;
use AdnanMula\Cards\Entrypoint\Controller\Shared\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

final class GetGamesController extends Controller
{
    public function __invoke(Request $request): JsonResponse
    {
        $this->assertIsLogged();

        $queryFilters = $request->query->all();
        [$orderField, $orderDirection] = $this->getOrder($request);

        $result = $this->extractResult(
            $this->bus->dispatch(new GetGamesQuery(
                deckId: $request->get('deckId'),
                userId: $request->get('userId'),
                winners: $queryFilters['extraFilterWinner'] ?? [],
                losers: $queryFilters['extraFilterLoser'] ?? [],
                loserScores: $queryFilters['extraFilterScore'] ?? [],
                competitions: $queryFilters['extraFilterCompetition'] ?? [],
                approved: true,
                start: $request->get('start'),
                length: $request->get('length'),
                orderField: $orderField,
                orderDirection: $orderDirection,
            )),
        );

        $response = [
            'recordsTotal' => $result['total'],
            'recordsFiltered' => $result['totalFiltered'],
            'data' => $result['games'],
            'draw' => (int) $request->get('draw'),
        ];

        return new JsonResponse($response);
    }

    private function getOrder(Request $request): array
    {
        $queryOrder = $request->get('order');

        if (\count($queryOrder) > 0) {
            $orderColumns = [6 => 'date'];

            $orderField = $orderColumns[(int)$queryOrder[0]['column']] ?? null;
            $orderType = $queryOrder[0]['dir'] ?? null;

            return [$orderField, $orderType];
        }

        return [null, null];
    }
}
