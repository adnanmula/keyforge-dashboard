<?php declare(strict_types=1);

namespace AdnanMula\Cards\Entrypoint\Controller\Keyforge\Stats\Game;

use AdnanMula\Cards\Application\Query\Keyforge\Game\GetGamesQuery;
use AdnanMula\Cards\Domain\Model\Shared\Filter;
use AdnanMula\Cards\Domain\Model\Shared\Pagination;
use AdnanMula\Cards\Domain\Model\Shared\QueryOrder;
use AdnanMula\Cards\Domain\Model\Shared\SearchTerm;
use AdnanMula\Cards\Domain\Model\Shared\SearchTerms;
use AdnanMula\Cards\Domain\Model\Shared\SearchTermType;
use AdnanMula\Cards\Entrypoint\Controller\Shared\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

final class GetGamesController extends Controller
{
    public function __invoke(Request $request): JsonResponse
    {
        $result = $this->extractResult(
            $this->bus->dispatch(new GetGamesQuery(
                $this->getPagination($request),
                $this->getSearch($request),
                $this->getOrder($request),
            )),
        );

        $response = [
            'recordsTotal' => $result['total'],
            'recordsFiltered' => $result['totalFiltered'],
            'data' => $result['games'],
        ];

        return new JsonResponse($response);
    }

    private function getPagination(Request $request): ?Pagination
    {
        $start = $request->get('start');
        $length = $request->get('length');

        if (null === $start || null === $length) {
            return null;
        }

        return new Pagination(
            (int) $request->get('start'),
            (int) $request->get('length'),
        );
    }

    private function getSearch(Request $request): ?SearchTerms
    {
        $deckId = $request->get('deckId');
        $userId = $request->get('userId');

        $filters = [];

        if (null !== $deckId) {
            $filters[] = new SearchTerm(
                SearchTermType::OR,
                new Filter('winner_deck', $deckId),
                new Filter('loser_deck', $deckId),
            );
        }

        if (null !== $userId) {
            $filters[] = new SearchTerm(
                SearchTermType::OR,
                new Filter('winner', $userId),
                new Filter('loser', $userId),
            );
        }

        if (\count($filters) === 0) {
            return null;
        }

        return new SearchTerms(...$filters);
    }

    private function getOrder(Request $request): ?QueryOrder
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
                $order = new QueryOrder(field: $orderField, order: $orderType);
            }
        }

        return $order;
    }
}
