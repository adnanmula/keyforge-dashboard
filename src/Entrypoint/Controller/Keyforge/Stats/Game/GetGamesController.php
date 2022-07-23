<?php declare(strict_types=1);

namespace AdnanMula\Cards\Entrypoint\Controller\Keyforge\Stats\Game;

use AdnanMula\Cards\Application\Query\Keyforge\Game\GetGamesByDeckQuery;
use AdnanMula\Cards\Domain\Model\Shared\Pagination;
use AdnanMula\Cards\Entrypoint\Controller\Shared\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

final class GetGamesController extends Controller
{
    public function __invoke(Request $request): JsonResponse
    {
        $deckId = $request->get('deckId');

        $result = $this->extractResult(
            $this->bus->dispatch(new GetGamesByDeckQuery(
                $deckId,
                new Pagination(
                    (int) $request->get('start'),
                    (int) $request->get('length'),
                ),
                null,
                null,
            )),
        );

        $response = [
            'recordsTotal' => $result['total'],
            'recordsFiltered' => $result['totalFiltered'],
            'data' => $result['games'],
        ];

        return new JsonResponse($response);
    }
}
