<?php declare(strict_types=1);

namespace AdnanMula\Cards\Application\Query\Keyforge\Game;

final readonly class GetGameStatsQuery
{
    public function __construct(
        public ?string $userId,
        public ?string $deckId,
        public array $winners,
        public array $losers,
        public array $loserScores,
        public array $competitions,
        public ?string $dateFrom,
        public ?string $dateTo,
        public array $logStats = [],
    ) {
    }
}
