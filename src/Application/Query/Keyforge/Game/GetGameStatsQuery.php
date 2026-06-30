<?php declare(strict_types=1);

namespace AdnanMula\Cards\Application\Query\Keyforge\Game;

use AdnanMula\Cards\Domain\Model\Keyforge\Game\ValueObject\KeyforgeCompetition;
use AdnanMula\Cards\Domain\Model\Shared\ValueObject\Uuid;
use Assert\Assert;

final readonly class GetGameStatsQuery
{
    public ?Uuid $userId;
    public ?Uuid $deckId;
    public array $winners;
    public array $losers;
    public array $loserScores;
    public array $competitions;
    public ?string $dateFrom;
    public ?string $dateTo;
    public array $logStats;

    public function __construct(
        mixed $userId,
        mixed $deckId,
        mixed $winners,
        mixed $losers,
        mixed $loserScores,
        mixed $competitions,
        mixed $dateFrom,
        mixed $dateTo,
        mixed $logStats = [],
    ) {
        Assert::lazy()
            ->that($userId, 'userId')->nullOr()->uuid()
            ->that($deckId, 'deckId')->nullOr()->uuid()
            ->that($winners, 'winners')->all()->uuid()
            ->that($losers, 'losers')->all()->uuid()
            ->that($loserScores, 'loserScores')->all()->integerish()
            ->that($competitions, 'competitions')->all()->inArray(KeyforgeCompetition::values())
            ->that($dateFrom, 'dateFrom')->nullOr()->date('Y-m-d')
            ->that($dateTo, 'dateTo')->nullOr()->date('Y-m-d')
            ->that($logStats, 'logStats')->isArray()
            ->verifyNow();

        $this->userId = Uuid::fromNullable($userId);
        $this->deckId = Uuid::fromNullable($deckId);
        $this->winners = $winners;
        $this->losers = $losers;
        $this->loserScores = $loserScores;
        $this->competitions = $competitions;
        $this->dateFrom = $dateFrom;
        $this->dateTo = $dateTo;
        $this->logStats = $logStats;
    }
}
