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

        $searchDeck = null;

        if (null !== $request->get('search') && '' !== $request->get('search')['value']) {
            $searchDeck = $request->get('search')['value'];
        }

        [$orderField, $orderDirection] = $this->orderBy($request->get('order'));

        $decks = $this->extractResult(
            $this->bus->dispatch(new GetDecksQuery(
                $request->get('start'),
                $request->get('length'),
                $searchDeck,
                $orderField,
                $orderDirection,
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

    public function orderBy(?array $queryOrder): array
    {
        $orderColumns = [
            1 => 'name',
            2 => 'set',
            4 => 'win_rate',
            5 => 'sas',
            6 => 'amber_control',
            7 => 'expected_amber',
            8 => 'artifact_control',
            9 => 'creature_control',
            10 => 'efficiency',
            11 => 'recursion',
            12 => 'disruption',
            13 => 'effective_power',
            14 => 'creature_protection',
            15 => 'total_armor',
            16 => 'creature_count',
            17 => 'action_count',
            18 => 'artifact_count',
            19 => 'upgrade_count',
            20 => 'key_cheat_count',
            21 => 'card_archive_count',
            22 => 'board_clear_count',
            23 => 'scaling_amber_control_count',
            24 => 'raw_amber',
            25 => 'aerc_score',
            26 => 'synergy_rating',
            27 => 'anti_synergy_rating',
        ];

        return [
            $orderColumns[(int) $queryOrder[0]['column']] ?? null,
            $queryOrder[0]['dir'] ?? null,
        ];
    }
}
