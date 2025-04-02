<?php declare(strict_types=1);

namespace AdnanMula\Cards\Entrypoint\Controller\Keyforge\Stats\Deck;

use AdnanMula\Cards\Application\Query\Keyforge\Deck\GetDecksQuery;
use AdnanMula\Cards\Domain\Model\Keyforge\Deck\ValueObject\KeyforgeCardRarity;
use AdnanMula\Cards\Domain\Model\Keyforge\Deck\ValueObject\KeyforgeDeckType;
use AdnanMula\Cards\Domain\Model\Keyforge\Deck\ValueObject\KeyforgeHouse;
use AdnanMula\Cards\Domain\Model\Keyforge\Deck\ValueObject\KeyforgeSet;
use AdnanMula\Cards\Entrypoint\Controller\Shared\Controller;
use Assert\LazyAssertionException;
use OpenApi\Attributes as OA;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

final class GetDecksController extends Controller
{
    #[OA\Get(
        path: '/decks/json',
        description: 'Retrieve a paginated list of Keyforge decks based on filters.',
        summary: 'Get list of decks',
        tags: ['Decks'],
        parameters: [
            new OA\Parameter(name: 'extraDeckId', description: 'Filter by deck ID', in: 'query', required: false, schema: new OA\Schema(type: 'string', format: 'uuid')),
            new OA\Parameter(name: 'extraFilterOwner', description: 'Filter by deck owner', in: 'query', required: false, schema: new OA\Schema(type: 'string', format: 'uuid')),
            new OA\Parameter(name: 'extraFilterOwners', description: 'Filter by multiple deck owners', in: 'query', required: false, schema: new OA\Schema(type: 'array', items: new OA\Items(type: 'string', format: 'uuid'))),
            new OA\Parameter(name: 'extraFilterSet', description: 'Filter by deck set', in: 'query', required: false, schema: new OA\Schema(type: 'string')),
            new OA\Parameter(name: 'extraFilterMaxSas', description: 'Maximum SAS filter', in: 'query', required: false, schema: new OA\Schema(type: 'integer')),
            new OA\Parameter(name: 'extraFilterMinSas', description: 'Minimum SAS filter', in: 'query', required: false, schema: new OA\Schema(type: 'integer')),
            new OA\Parameter(
                name: 'extraFilterDeckTypes',
                description: 'Filter by deck types',
                in: 'query',
                required: false,
                schema: new OA\Schema(
                type: 'array', items: new OA\Items(type: 'string', enum: [
                    KeyforgeDeckType::STANDARD,
                    KeyforgeDeckType::ALLIANCE,
                    KeyforgeDeckType::THEORETICAL,
                ])),
            ),
            new OA\Parameter(name: 'extraFilterOnlyFriends', description: 'Filter only friends', in: 'query', required: false, schema: new OA\Schema(type: 'boolean')),
            new OA\Parameter(name: 'extraFilterOnlyOwned', description: 'Filter only owned decks', in: 'query', required: false, schema: new OA\Schema(type: 'boolean')),
            new OA\Parameter(name: 'extraFilterTagType', description: 'Defines the type of tag filter. Use it with extraFilterTags and extraFilterExcluded', in: 'query', required: false, schema: new OA\Schema(type: 'string', enum: ['all', 'any'])),
            new OA\Parameter(name: 'extraFilterTags', description: 'Filter by tags', in: 'query', required: false, schema: new OA\Schema(type: 'array', items: new OA\Items(type: 'string', format: 'uuid'))),
            new OA\Parameter(name: 'extraFilterTagsExcluded', description: 'Exclude decks with these tags', in: 'query', required: false, schema: new OA\Schema(type: 'array', items: new OA\Items(type: 'string', format: 'uuid'))),
            new OA\Parameter(name: 'extraFilterHouseFilterType', description: 'Defines the type of house filter. Use it with extraFilterHouses', in: 'query', required: false, schema: new OA\Schema(type: 'string', enum: ['all', 'any'])),
            new OA\Parameter(
                name: 'extraFilterHouses',
                description: 'Filter by houses',
                in: 'query',
                required: false,
                schema: new OA\Schema(
                    type: 'array',
                    items: new OA\Items(type: 'string', enum: [
                        KeyforgeHouse::BROBNAR,
                        KeyforgeHouse::DIS,
                        KeyforgeHouse::MARS,
                        KeyforgeHouse::SHADOWS,
                        KeyforgeHouse::UNTAMED,
                        KeyforgeHouse::SANCTUM,
                        KeyforgeHouse::LOGOS,
                        KeyforgeHouse::SAURIAN,
                        KeyforgeHouse::STAR_ALLIANCE,
                        KeyforgeHouse::UNFATHOMABLE,
                        KeyforgeHouse::EKWIDON,
                        KeyforgeHouse::GEISTOID,
                        KeyforgeHouse::SKYBORN,
                        KeyforgeHouse::REDEMPTION,
                    ]),
                )
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
                            new OA\Property(property: 'dir', description: 'Sort direction', type: 'string', enum: ['asc', 'desc'])
                        ],
                        type: 'object'
                    )
                )
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
                        new OA\Property(property: 'data', description: 'List of decks', type: 'array', items: new OA\Items(properties: [
                            new OA\Property(property: 'id', description: 'Deck ID', type: 'string', format: 'uuid'),
                            new OA\Property(property: 'dok_id', description: 'Deck ID on dok', type: 'string', format: 'integer'),
                            new OA\Property(property: 'name', description: 'Deck name', type: 'string'),
                            new OA\Property(property: 'type', description: 'Deck type', type: 'string', enum: [
                                KeyforgeDeckType::STANDARD,
                                KeyforgeDeckType::ALLIANCE,
                                KeyforgeDeckType::THEORETICAL,
                            ]),
                            new OA\Property(property: 'set', description: 'Deck set', type: 'string', enum: [
                                KeyforgeSet::CotA,
                                KeyforgeSet::AoA,
                                KeyforgeSet::WC,
                                KeyforgeSet::MM,
                                KeyforgeSet::DT,
                                KeyforgeSet::WoE,
                                KeyforgeSet::GR,
                                KeyforgeSet::AS,
                                KeyforgeSet::ToC,
                                KeyforgeSet::MoM,
                                KeyforgeSet::DIS,
                                KeyforgeSet::U22,
                                KeyforgeSet::M24,
                                KeyforgeSet::VM23,
                                KeyforgeSet::VM24,
                            ]),
                            new OA\Property(
                                property: 'houses',
                                description: 'Deck houses',
                                type: 'array',
                                items: new OA\Items(type: 'string', enum: [
                                    KeyforgeHouse::BROBNAR,
                                    KeyforgeHouse::DIS,
                                    KeyforgeHouse::MARS,
                                    KeyforgeHouse::SHADOWS,
                                    KeyforgeHouse::UNTAMED,
                                    KeyforgeHouse::SANCTUM,
                                    KeyforgeHouse::LOGOS,
                                    KeyforgeHouse::SAURIAN,
                                    KeyforgeHouse::STAR_ALLIANCE,
                                    KeyforgeHouse::UNFATHOMABLE,
                                    KeyforgeHouse::EKWIDON,
                                    KeyforgeHouse::GEISTOID,
                                    KeyforgeHouse::SKYBORN,
                                    KeyforgeHouse::REDEMPTION,
                                ]),
                                maxItems: 3,
                                minItems: 3,
                            ),
                            new OA\Property(
                                property: 'tags',
                                description: 'List of tags (UUIDs)',
                                type: 'array',
                                items: new OA\Items(
                                    type: 'string',
                                    format: 'uuid'
                                ),
                            ),
                            new OA\Property(
                                property: 'owners',
                                description: 'List of owners (UUIDs)',
                                type: 'array',
                                items: new OA\Items(
                                    type: 'string',
                                    format: 'uuid'
                                ),
                            ),
                            new OA\Property(
                                property: 'stats',
                                description: 'Deck stats',
                                properties: [
                                    new OA\Property(property: 'sas', type: 'integer', example: 73),
                                    new OA\Property(property: 'previousSasRating', type: 'integer', example: 71),
                                    new OA\Property(property: 'previousMajorSasRating', type: 'integer', example: 69),
                                    new OA\Property(property: 'sasPercentile', type: 'number', format: 'float', example: 94.99),
                                    new OA\Property(property: 'sasVersion', type: 'integer', example: 46),
                                    new OA\Property(property: 'aercScore', type: 'integer', example: 59),
                                    new OA\Property(property: 'aercVersion', type: 'integer', example: -1),
                                    new OA\Property(property: 'amberControl', type: 'number', format: 'float', example: 9.95),
                                    new OA\Property(property: 'artifactControl', type: 'number', format: 'float', example: 4.3),
                                    new OA\Property(property: 'expectedAmber', type: 'number', format: 'float', example: 30.08),
                                    new OA\Property(property: 'creatureControl', type: 'number', format: 'float', example: 3.33),
                                    new OA\Property(property: 'efficiency', type: 'number', format: 'float', example: 16.27),
                                    new OA\Property(property: 'recursion', type: 'number', format: 'float', example: 1.23),
                                    new OA\Property(property: 'disruption', type: 'integer', example: 1),
                                    new OA\Property(property: 'effectivePower', type: 'integer', example: 50),
                                    new OA\Property(property: 'creatureProtection', type: 'number', format: 'float', example: 0.5),
                                    new OA\Property(property: 'other', type: 'number', format: 'float', example: 0.33),
                                    new OA\Property(property: 'rawAmber', type: 'integer', example: 15),
                                    new OA\Property(property: 'totalPower', type: 'integer', example: 40),
                                    new OA\Property(property: 'totalArmor', type: 'integer', example: 0),
                                    new OA\Property(property: 'efficiencyBonus', type: 'number', format: 'float', example: 1.02),
                                    new OA\Property(property: 'creatureCount', type: 'integer', example: 14),
                                    new OA\Property(property: 'actionCount', type: 'integer', example: 20),
                                    new OA\Property(property: 'artifactCount', type: 'integer', example: 0),
                                    new OA\Property(property: 'upgradeCount', type: 'integer', example: 2),
                                    new OA\Property(property: 'cardDrawCount', type: 'integer', example: 4),
                                    new OA\Property(property: 'cardArchiveCount', type: 'integer', example: 2),
                                    new OA\Property(property: 'keyCheatCount', type: 'integer', example: 0),
                                    new OA\Property(property: 'boardClearCount', type: 'integer', example: 1),
                                    new OA\Property(
                                        property: 'boardClearCards',
                                        type: 'array',
                                        items: new OA\Items(type: 'string'),
                                        example: ["Poison Wave"]
                                    ),
                                    new OA\Property(property: 'scalingAmberControlCount', type: 'integer', example: 1),
                                    new OA\Property(
                                        property: 'scalingAmberControlCards',
                                        type: 'array',
                                        items: new OA\Items(type: 'string'),
                                        example: ["Interdimensional Graft"]
                                    ),
                                    new OA\Property(property: 'synergyRating', type: 'integer', example: 15),
                                    new OA\Property(property: 'antiSynergyRating', type: 'integer', example: 1),
                                    new OA\Property(property: 'lastSasUpdate', type: 'string', format: 'date', example: "2023-07-21"),
                                ],
                                type: 'object'
                            ),
                            new OA\Property(
                                property: 'alliance_composition',
                                description: 'If the deck is alliance defines it\'s composition, null otherwise',
                                type: 'array',
                                items: new OA\Items(
                                    properties: [
                                        new OA\Property(property: 'name', type: 'string', example: 'Bishop el Sereno'),
                                        new OA\Property(property: 'house', type: 'string', example: 'Dis'),
                                        new OA\Property(property: 'keyforgeId', type: 'string', format: 'uuid', example: '4ba628c1-15e4-463d-a29c-c6827b6d9e75')
                                    ],
                                    type: 'object'
                                ),
                                maxItems: 3,
                                minItems: 3,
                                example: [
                                    [
                                        "name" => "Bishop el Sereno",
                                        "house" => "Dis",
                                        "keyforgeId" => "4ba628c1-15e4-463d-a29c-c6827b6d9e75"
                                    ],
                                    [
                                        "name" => "“Vampiro”, Carroñero de Shrikesswan",
                                        "house" => "Shadows",
                                        "keyforgeId" => "7c686603-adb8-4c76-8128-8656a551b67e"
                                    ],
                                    [
                                        "name" => "Bjorn, Anciano de la Prisión Piadosa",
                                        "house" => "Untamed",
                                        "keyforgeId" => "e950b681-e729-427c-bb3b-f7739469a975"
                                    ]
                                ],
                                nullable: true
                            ),
                            new OA\Property(
                                property: 'userData',
                                description: 'User statistics of deck',
                                properties: [
                                    new OA\Property(property: 'deckId', type: 'string', format: 'uuid', example: '1f7f67ac-1be8-4dc4-bc5b-9d67b34ec523'),
                                    new OA\Property(property: 'userId', type: 'string', format: 'uuid', example: null, nullable: true),
                                    new OA\Property(property: 'wins', type: 'integer', example: 24),
                                    new OA\Property(property: 'losses', type: 'integer', example: 32),
                                    new OA\Property(property: 'wins_vs_friends', type: 'integer', example: 6),
                                    new OA\Property(property: 'losses_vs_friends', type: 'integer', example: 12),
                                    new OA\Property(property: 'wins_vs_users', type: 'integer', example: 6),
                                    new OA\Property(property: 'losses_vs_users', type: 'integer', example: 12)
                                ],
                                type: 'object'
                            ),
                            new OA\Property(
                                property: 'cards',
                                description: 'Cards information with first pod house and cards data',
                                properties: [
                                    new OA\Property(
                                        property: 'extraCards',
                                        type: 'array',
                                        items: new OA\Items(
                                            properties: [
                                                new OA\Property(property: 'name', type: 'string', example: 'Phantasm'),
                                                new OA\Property(property: 'serializedName', type: 'string', example: 'phantasm'),
                                                new OA\Property(property: 'imageUrl', type: 'string', format: 'url', example: 'https://...'),
                                                new OA\Property(property: 'type', type: 'string', example: 'token-creature'),
                                            ],
                                            type: 'object'
                                        ),
                                    ),
                                    new OA\Property(
                                        property: 'firstPodHouse',
                                        description: 'House of the first pod',
                                        type: 'string',
                                        enum: [
                                            KeyforgeHouse::BROBNAR,
                                            KeyforgeHouse::DIS,
                                            KeyforgeHouse::MARS,
                                            KeyforgeHouse::SHADOWS,
                                            KeyforgeHouse::UNTAMED,
                                            KeyforgeHouse::SANCTUM,
                                            KeyforgeHouse::LOGOS,
                                            KeyforgeHouse::SAURIAN,
                                            KeyforgeHouse::STAR_ALLIANCE,
                                            KeyforgeHouse::UNFATHOMABLE,
                                            KeyforgeHouse::EKWIDON,
                                            KeyforgeHouse::GEISTOID,
                                            KeyforgeHouse::SKYBORN,
                                            KeyforgeHouse::REDEMPTION,
                                        ],
                                        example: 'LOGOS',
                                    ),
                                    new OA\Property(
                                        property: 'firstPodCards',
                                        type: 'array',
                                        items: new OA\Items(
                                            properties: [
                                                new OA\Property(property: 'name', type: 'string', example: 'Dimension Door'),
                                                new OA\Property(property: 'serializedName', type: 'string', example: 'dimension-door'),
                                                new OA\Property(property: 'imageUrl', type: 'string', example: 'dimension-door'),
                                                new OA\Property(property: 'rarity', type: 'string', enum: [
                                                    KeyforgeCardRarity::RARE,
                                                    KeyforgeCardRarity::COMMON,
                                                    KeyforgeCardRarity::UNCOMMON,
                                                    KeyforgeCardRarity::FIXED,
                                                    KeyforgeCardRarity::SPECIAL,
                                                    KeyforgeCardRarity::VARIANT,
                                                    KeyforgeCardRarity::EVILTWIN,
                                                ], example: 'UNCOMMON'),
                                                new OA\Property(property: 'isEnhanced', type: 'boolean', example: false),
                                                new OA\Property(property: 'isMaverick', type: 'boolean', example: false),
                                                new OA\Property(property: 'isLegacy', type: 'boolean', example: false),
                                                new OA\Property(property: 'isAnomaly', type: 'boolean', example: false),
                                                new OA\Property(property: 'bonusAember', type: 'integer', example: 0),
                                                new OA\Property(property: 'bonusCapture', type: 'integer', example: 0),
                                                new OA\Property(property: 'bonusDamage', type: 'integer', example: 0),
                                                new OA\Property(property: 'bonusDraw', type: 'integer', example: 0),
                                                new OA\Property(property: 'bonusDiscard', type: 'integer', example: 0)
                                            ],
                                            type: 'object'
                                        ),
                                        maxItems: 12,
                                        minItems: 12
                                    ),
                                    new OA\Property(
                                        property: 'secondPodHouse',
                                        description: 'House of the second pod',
                                        type: 'string',
                                        enum: [
                                            KeyforgeHouse::BROBNAR,
                                            KeyforgeHouse::DIS,
                                            KeyforgeHouse::MARS,
                                            KeyforgeHouse::SHADOWS,
                                            KeyforgeHouse::UNTAMED,
                                            KeyforgeHouse::SANCTUM,
                                            KeyforgeHouse::LOGOS,
                                            KeyforgeHouse::SAURIAN,
                                            KeyforgeHouse::STAR_ALLIANCE,
                                            KeyforgeHouse::UNFATHOMABLE,
                                            KeyforgeHouse::EKWIDON,
                                            KeyforgeHouse::GEISTOID,
                                            KeyforgeHouse::SKYBORN,
                                            KeyforgeHouse::REDEMPTION,
                                        ],
                                        example: 'LOGOS',
                                    ),
                                    new OA\Property(
                                        property: 'secondPodCards',
                                        type: 'array',
                                        items: new OA\Items(
                                            properties: [
                                                new OA\Property(property: 'name', type: 'string', example: 'Dimension Door'),
                                                new OA\Property(property: 'serializedName', type: 'string', example: 'dimension-door'),
                                                new OA\Property(property: 'imageUrl', type: 'string', example: 'dimension-door'),
                                                new OA\Property(property: 'rarity', type: 'string', enum: [
                                                    KeyforgeCardRarity::RARE,
                                                    KeyforgeCardRarity::COMMON,
                                                    KeyforgeCardRarity::UNCOMMON,
                                                    KeyforgeCardRarity::FIXED,
                                                    KeyforgeCardRarity::SPECIAL,
                                                    KeyforgeCardRarity::VARIANT,
                                                    KeyforgeCardRarity::EVILTWIN,
                                                ], example: 'UNCOMMON'),
                                                new OA\Property(property: 'isEnhanced', type: 'boolean', example: false),
                                                new OA\Property(property: 'isMaverick', type: 'boolean', example: false),
                                                new OA\Property(property: 'isLegacy', type: 'boolean', example: false),
                                                new OA\Property(property: 'isAnomaly', type: 'boolean', example: false),
                                                new OA\Property(property: 'bonusAember', type: 'integer', example: 0),
                                                new OA\Property(property: 'bonusCapture', type: 'integer', example: 0),
                                                new OA\Property(property: 'bonusDamage', type: 'integer', example: 0),
                                                new OA\Property(property: 'bonusDraw', type: 'integer', example: 0),
                                                new OA\Property(property: 'bonusDiscard', type: 'integer', example: 0)
                                            ],
                                            type: 'object'
                                        ),
                                        maxItems: 12,
                                        minItems: 12
                                    ),
                                    new OA\Property(
                                        property: 'thirdPodHouse',
                                        description: 'House of the third pod',
                                        type: 'string',
                                        enum: [
                                            KeyforgeHouse::BROBNAR,
                                            KeyforgeHouse::DIS,
                                            KeyforgeHouse::MARS,
                                            KeyforgeHouse::SHADOWS,
                                            KeyforgeHouse::UNTAMED,
                                            KeyforgeHouse::SANCTUM,
                                            KeyforgeHouse::LOGOS,
                                            KeyforgeHouse::SAURIAN,
                                            KeyforgeHouse::STAR_ALLIANCE,
                                            KeyforgeHouse::UNFATHOMABLE,
                                            KeyforgeHouse::EKWIDON,
                                            KeyforgeHouse::GEISTOID,
                                            KeyforgeHouse::SKYBORN,
                                            KeyforgeHouse::REDEMPTION,
                                        ],
                                        example: 'LOGOS',
                                    ),
                                    new OA\Property(
                                        property: 'thirdPodCards',
                                        type: 'array',
                                        items: new OA\Items(
                                            properties: [
                                                new OA\Property(property: 'name', type: 'string', example: 'Dimension Door'),
                                                new OA\Property(property: 'serializedName', type: 'string', example: 'dimension-door'),
                                                new OA\Property(property: 'imageUrl', type: 'string', example: 'dimension-door'),
                                                new OA\Property(property: 'rarity', type: 'string', enum: [
                                                    KeyforgeCardRarity::RARE,
                                                    KeyforgeCardRarity::COMMON,
                                                    KeyforgeCardRarity::UNCOMMON,
                                                    KeyforgeCardRarity::FIXED,
                                                    KeyforgeCardRarity::SPECIAL,
                                                    KeyforgeCardRarity::VARIANT,
                                                    KeyforgeCardRarity::EVILTWIN,
                                                ], example: 'UNCOMMON'),
                                                new OA\Property(property: 'isEnhanced', type: 'boolean', example: false),
                                                new OA\Property(property: 'isMaverick', type: 'boolean', example: false),
                                                new OA\Property(property: 'isLegacy', type: 'boolean', example: false),
                                                new OA\Property(property: 'isAnomaly', type: 'boolean', example: false),
                                                new OA\Property(property: 'bonusAember', type: 'integer', example: 0),
                                                new OA\Property(property: 'bonusCapture', type: 'integer', example: 0),
                                                new OA\Property(property: 'bonusDamage', type: 'integer', example: 0),
                                                new OA\Property(property: 'bonusDraw', type: 'integer', example: 0),
                                                new OA\Property(property: 'bonusDiscard', type: 'integer', example: 0)
                                            ],
                                            type: 'object'
                                        ),
                                        maxItems: 12,
                                        minItems: 12
                                    ),
                                ],
                                type: 'object'
                            ),
                        ], type: 'object')),
                        new OA\Property(property: 'draw', description: 'Draw counter for DataTables', type: 'integer'),
                    ],
                    type: 'object'
                )
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
                )
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
                )
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
                )
            ),
        ],
    )]
    public function __invoke(Request $request): JsonResponse
    {
        try {
            $this->assertIsLogged();
        } catch (AccessDeniedException) {
            return new JsonResponse(['error' => 'Access denied'], Response::HTTP_FORBIDDEN);
        }

        $user = $this->getUser();

        $searchDeck = null;

        if (null !== $request->get('search') && '' !== $request->get('search')['value']) {
            $searchDeck = $request->get('search')['value'];
        }

        [$orderField, $orderDirection] = $this->orderBy($request->get('order'));

        try {
            $result = $this->bus->dispatch(new GetDecksQuery(
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
            ));
        } catch (LazyAssertionException $e) {
            return new JsonResponse(['error' => $e->getMessage()], Response::HTTP_BAD_REQUEST);
        } catch (\Throwable $e) {
            return new JsonResponse(['error' => $e->getMessage()], Response::HTTP_CONFLICT);
        }

        $decks = $this->extractResult($result);

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
