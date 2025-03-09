<?php declare(strict_types=1);

namespace AdnanMula\Cards\Entrypoint\Controller\Keyforge\Stats\Deck;

use AdnanMula\Cards\Application\Query\Keyforge\Deck\GetDecksQuery;
use AdnanMula\Cards\Entrypoint\Controller\Shared\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

final class GetDecksController extends Controller
{
    public function __invoke(Request $request): JsonResponse
    {
        $user = $this->getUser();

        $queryOrder = $request->get('order');

        $searchDeck = null;

        if (null !== $request->get('search') && '' !== $request->get('search')['value']) {
            $searchDeck = $request->get('search')['value'];
        }

        $decks = $this->extractResult(
            $this->bus->dispatch(new GetDecksQuery(
                $request->get('start'),
                $request->get('length'),
                $searchDeck,
                $queryOrder,
                $request->query->all()['extraFilterSet'] ?? null,
                null === $request->query->get('extraFilterHouseFilterType')
                    ? null
                    : (string) $request->query->get('extraFilterHouseFilterType'),
                $request->query->all()['extraFilterHouses'] ?? null,
                $request->query->all()['extraFilterDeckTypes'] ?? null,
                $request->get('extraDeckId'),
                $request->get('extraFilterOwner'),
                $request->query->all()['extraFilterOwners'] ?? [],
                false,
                $request->get('extraFilterTagType'),
                $request->query->all()['extraFilterTags'] ?? [],
                $request->query->all()['extraFilterTagsExcluded'] ?? [],
                $request->get('extraFilterMaxSas', 150),
                $request->get('extraFilterMinSas', 0),
                $request->get('extraFilterOnlyFriends') === 'true' ? $user?->id()->value() : null,
            )),
        );

        $response = [
            'data' => $decks['decks'],
            'draw' => (int) $request->get('draw'),
            'recordsFiltered' => $decks['totalFiltered'],
            'recordsTotal' => $decks['total'],
        ];

        return new JsonResponse($response);
    }
}
