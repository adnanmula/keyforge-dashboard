<?php declare(strict_types=1);

namespace AdnanMula\Cards\Entrypoint\Controller\Keyforge\Game\List;

use AdnanMula\Cards\Application\Query\Keyforge\Game\GetGamesQuery;
use AdnanMula\Cards\Domain\Model\Keyforge\Game\ValueObject\KeyforgeCompetition;
use AdnanMula\Cards\Entrypoint\Controller\Shared\Controller;
use AdnanMula\Criteria\Sorting\OrderType;
use Assert\LazyAssertionException;
use OpenApi\Attributes as OA;
use Symfony\Component\HttpFoundation\InputBag;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

final class GetGamesController extends Controller
{
    #[OA\Get(
        path: '/games/json',
        description: 'Retrieve a paginated list of Keyforge games based on filters.',
        summary: 'Get list of games',
        tags: ['Games'],
        parameters: [
            new OA\Parameter(name: 'deckId', description: 'Filter by deck ID', in: 'query', required: false, schema: new OA\Schema(type: 'string', format: 'uuid')),
            new OA\Parameter(name: 'userId', description: 'Filter by user ID', in: 'query', required: false, schema: new OA\Schema(type: 'string', format: 'uuid')),
            new OA\Parameter(
                name: 'extraFilterWinner',
                description: 'Filter by winners',
                in: 'query',
                required: false,
                schema: new OA\Schema(type: 'array', items: new OA\Items(type: 'string', format: 'uuid')),
            ),
            new OA\Parameter(
                name: 'extraFilterLoser',
                description: 'Filter by losers',
                in: 'query',
                required: false,
                schema: new OA\Schema(type: 'array', items: new OA\Items(type: 'string', format: 'uuid')),
            ),
            new OA\Parameter(
                name: 'extraFilterScore',
                description: 'Filter by loser scores',
                in: 'query',
                required: false,
                schema: new OA\Schema(
                    type: 'array',
                    items: new OA\Items(
                        type: 'integer',
                        enum: [0, 1, 2],
                    ),
                ),
            ),
            new OA\Parameter(
                name: 'extraFilterCompetition',
                description: 'Filter by competitions',
                in: 'query',
                required: false,
                schema: new OA\Schema(
                    type: 'array',
                    items: new OA\Items(
                        type: 'string',
                        enum: [
                            KeyforgeCompetition::SOLO,
                            KeyforgeCompetition::FRIENDS,
                            KeyforgeCompetition::TCO_CASUAL,
                            KeyforgeCompetition::TCO_COMPETITIVE,
                            KeyforgeCompetition::LOCAL_LEAGUE,
                            KeyforgeCompetition::FRIENDS_LEAGUE,
                            KeyforgeCompetition::VT,
                            KeyforgeCompetition::LGS,
                            KeyforgeCompetition::NKFL,
                        ],
                    ),
                ),
            ),
            new OA\Parameter(name: 'start', description: 'Pagination start index', in: 'query', required: false, schema: new OA\Schema(type: 'integer')),
            new OA\Parameter(name: 'length', description: 'Number of records per page', in: 'query', required: false, schema: new OA\Schema(type: 'integer')),
            new OA\Parameter(
                name: 'order',
                description: 'Ordering information',
                in: 'query',
                required: false,
                schema: new OA\Schema(
                    type: 'array',
                    items: new OA\Items(
                        properties: [
                            new OA\Property(property: 'column', description: 'Field to sort by', type: 'string'),
                            new OA\Property(property: 'dir', description: 'Sort direction', type: 'string', enum: ['asc', 'desc']),
                        ],
                        type: 'object',
                    ),
                ),
            ),
            new OA\Parameter(name: 'draw', description: 'Draw counter for DataTables', in: 'query', required: false, schema: new OA\Schema(type: 'integer')),
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Successful response',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'recordsTotal', description: 'Total number of records', type: 'integer'),
                        new OA\Property(property: 'recordsFiltered', description: 'Total number of filtered records', type: 'integer'),
                        new OA\Property(property: 'data', description: 'List of games', type: 'array', items: new OA\Items(properties: [
                            new OA\Property(property: 'winner', description: 'Winner ID', type: 'string', format: 'uuid'),
                            new OA\Property(property: 'winner_name', description: 'Winner username', type: 'string'),
                            new OA\Property(property: 'winner_deck', description: 'Winner deck ID', type: 'string', format: 'uuid'),
                            new OA\Property(property: 'winner_deck_name', description: 'Winner deck name', type: 'string'),
                            new OA\Property(property: 'loser', description: 'Loser ID', type: 'string', format: 'uuid'),
                            new OA\Property(property: 'loser_name', description: 'Loser username', type: 'string'),
                            new OA\Property(property: 'loser_deck', description: 'Loser deck ID', type: 'string', format: 'uuid'),
                            new OA\Property(property: 'loser_deck_name', description: 'Loser deck name', type: 'string'),
                            new OA\Property(property: 'score', description: 'Game score', type: 'string', enum: ['3/0', '3/1', '3/2']),
                            new OA\Property(property: 'first_turn', description: 'Player who took the first turn', type: 'string'),
                            new OA\Property(property: 'date', description: 'Date of the game (Y-m-d format)', type: 'string', format: 'date'),
                            new OA\Property(property: 'competition', description: 'Competition type', type: 'string', enum: [
                                KeyforgeCompetition::SOLO,
                                KeyforgeCompetition::FRIENDS,
                                KeyforgeCompetition::TCO_CASUAL,
                                KeyforgeCompetition::TCO_COMPETITIVE,
                                KeyforgeCompetition::LOCAL_LEAGUE,
                                KeyforgeCompetition::FRIENDS_LEAGUE,
                                KeyforgeCompetition::VT,
                                KeyforgeCompetition::LGS,
                                KeyforgeCompetition::NKFL,
                            ]),
                            new OA\Property(property: 'notes', description: 'Additional notes', type: 'string'),
                        ], type: 'object')),
                        new OA\Property(property: 'draw', description: 'Draw counter for DataTables', type: 'integer'),
                    ],
                    type: 'object',
                    example: [
                        "recordsTotal" => 2,
                        "recordsFiltered" => 2,
                        "data" => [
                            [
                                "winner" => "426117e9-e016-4f53-be1f-4eb8711ce625",
                                "winner_name" => "username",
                                "winner_deck" => "496b4258-b02c-4270-9918-4fd9c3366986",
                                "winner_deck_name" => "Lydia la Inacabable de la Colmena",
                                "loser" => "97a7e9fe-ff27-4d52-83c0-df4bc9309fb0",
                                "loser_name" => "username2",
                                "loser_deck" => "10ff6ac7-c6c9-444b-a1aa-10fe87c3c524",
                                "loser_deck_name" => "Parker la Sedienta",
                                "score" => "3/2",
                                "first_turn" => "username2",
                                "date" => "2022-07-23",
                                "competition" => "Friendly",
                                "notes" => "",
                            ],
                            [
                                "winner" => "426117e9-e016-4f53-be1f-4eb8711ce625",
                                "winner_name" => "username",
                                "winner_deck" => "aa99749f-79b3-4040-8cd7-5c824cf3da3b",
                                "winner_deck_name" => "Sátiro, Rebelde del Foro",
                                "loser" => "97a7e9fe-ff27-4d52-83c0-df4bc9309fb0",
                                "loser_name" => "username2",
                                "loser_deck" => "496b4258-b02c-4270-9918-4fd9c3366986",
                                "loser_deck_name" => "Lydia la Inacabable de la Colmena",
                                "score" => "3/2",
                                "first_turn" => "username2",
                                "date" => "2022-07-12",
                                "competition" => "Friendly",
                                "notes" => "",
                            ],
                        ],
                        "draw" => 1,
                    ],
                ),
            ),
            new OA\Response(
                response: 400,
                description: 'Invalid request parameters',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'error', description: 'Error message detailing validation failures', type: 'string'),
                    ],
                    example: [
                        'error' => 'The following 1 assertions failed:\n1) loserScores: Value \u0022A\u0022 is not an integer or a number castable to integer.\n',
                    ],
                ),
            ),
            new OA\Response(
                response: 403,
                description: 'Access denied',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'error', description: 'Error message', type: 'string'),
                    ],
                    example: [
                        'error' => 'Access denied',
                    ],
                ),
            ),
            new OA\Response(
                response: 409,
                description: 'Unexpected error',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'error', description: 'Error message', type: 'string'),
                    ],
                    example: [
                        'error' => 'Whatever error',
                    ],
                ),
            ),
        ],
    )]
    public function __invoke(Request $request): JsonResponse
    {
        $allParams = new InputBag(\array_merge($request->query->all(), $request->request->all()));
        [$orderField, $orderDirection] = $this->getOrder($allParams);

        $logStatKeys = [
            'turnsMin', 'turnsMax',
            'winnerAmberObtainedMin', 'winnerAmberObtainedMax',
            'winnerAmberStolenMin', 'winnerAmberStolenMax',
            'winnerCardsPlayedMin', 'winnerCardsPlayedMax',
            'winnerCardsDrawnMin', 'winnerCardsDrawnMax',
            'winnerCardsDiscardedMin', 'winnerCardsDiscardedMax',
            'winnerKeysForgedMin', 'winnerKeysForgedMax',
            'winnerFightsMin', 'winnerFightsMax',
            'winnerReapsMin', 'winnerReapsMax',
            'winnerExtraTurnsMin', 'winnerExtraTurnsMax',
            'loserAmberObtainedMin', 'loserAmberObtainedMax',
            'loserAmberStolenMin', 'loserAmberStolenMax',
            'loserCardsPlayedMin', 'loserCardsPlayedMax',
            'loserCardsDrawnMin', 'loserCardsDrawnMax',
            'loserCardsDiscardedMin', 'loserCardsDiscardedMax',
            'loserKeysForgedMin', 'loserKeysForgedMax',
            'loserFightsMin', 'loserFightsMax',
            'loserReapsMin', 'loserReapsMax',
            'loserExtraTurnsMin', 'loserExtraTurnsMax',
            'totalAmberObtainedMin', 'totalAmberObtainedMax',
            'totalAmberStolenMin', 'totalAmberStolenMax',
            'totalCardsPlayedMin', 'totalCardsPlayedMax',
            'totalCardsDrawnMin', 'totalCardsDrawnMax',
            'totalCardsDiscardedMin', 'totalCardsDiscardedMax',
            'totalKeysForgedMin', 'totalKeysForgedMax',
            'totalFightsMin', 'totalFightsMax',
            'totalReapsMin', 'totalReapsMax',
            'totalExtraTurnsMin', 'totalExtraTurnsMax',
        ];

        $logStats = [];
        foreach ($logStatKeys as $key) {
            $value = $allParams->get($key);
            if ($value !== null && $value !== '') {
                $logStats[$key] = $value;
            }
        }

        try {
            $games = $this->bus->dispatch(new GetGamesQuery(
                deckId: $allParams->get('deckId'),
                userId: $allParams->get('userId'),
                winners: $allParams->all()['extraFilterWinner'] ?? [],
                losers: $allParams->all()['extraFilterLoser'] ?? [],
                loserScores: $allParams->all()['extraFilterScore'] ?? [],
                competitions: $allParams->all()['extraFilterCompetition'] ?? [],
                approved: true,
                dateFrom: $allParams->get('extraFilterDateFrom') ?: null,
                dateTo: $allParams->get('extraFilterDateTo') ?: null,
                logStats: $logStats ?: null,
                start: $allParams->get('start'),
                length: $allParams->get('length'),
                orderField: $orderField,
                orderDirection: $orderDirection,
            ));
        } catch (LazyAssertionException $e) {
            return new JsonResponse(['error' => $e->getMessage()], Response::HTTP_BAD_REQUEST);
        } catch (\Throwable $e) {
            return new JsonResponse(['error' => $e->getMessage()], Response::HTTP_CONFLICT);
        }

        $result = $this->extractResult($games);

        $response = [
            'recordsTotal' => $result['total'],
            'recordsFiltered' => $result['totalFiltered'],
            'data' => $result['games'],
            'draw' => (int) $allParams->get('draw'),
        ];

        return new JsonResponse($response);
    }

    private function getOrder(InputBag $params): array
    {
        $queryOrder = $params->all()['order'];

        if (\count($queryOrder) > 0) {
            $orderColumns = [
                6 => 'date',
                8 => 'turns',
                9 => 'winner_amber_obtained',
                10 => 'winner_amber_stolen',
                11 => 'winner_cards_played',
                12 => 'winner_cards_drawn',
                13 => 'winner_cards_discarded',
                14 => 'winner_fights',
                15 => 'winner_reaps',
                16 => 'winner_extra_turns',
                17 => 'loser_amber_obtained',
                18 => 'loser_amber_stolen',
                19 => 'loser_cards_played',
                20 => 'loser_cards_drawn',
                21 => 'loser_cards_discarded',
                22 => 'loser_fights',
                23 => 'loser_reaps',
                24 => 'loser_extra_turns',
                25 => 'total_amber_obtained',
                26 => 'total_amber_stolen',
                27 => 'total_cards_played',
                28 => 'total_cards_drawn',
                29 => 'total_cards_discarded',
                30 => 'total_keys_forged',
                31 => 'total_fights',
                32 => 'total_reaps',
                33 => 'total_extra_turns',
            ];

            $column = (int) $queryOrder[0]['column'];
            $orderField = $orderColumns[$column] ?? null;
            $orderType = $queryOrder[0]['dir'] ?? null;

            if ($column >= 8) {
                if ($orderType === 'asc') {
                    $orderType = OrderType::ASC_NULLS_LAST->value;
                }

                if ($orderType === 'desc') {
                    $orderType = OrderType::DESC_NULLS_LAST->value;
                }
            }

            return [$orderField, $orderType];
        }

        return [null, null];
    }
}
