<?php declare(strict_types=1);

namespace AdnanMula\Cards\Domain\Model\Keyforge\Game;

use AdnanMula\Cards\Domain\Model\Shared\ValueObject\Uuid;

final readonly class KeyforgeGameLog implements \JsonSerializable
{
    public function __construct(
        public Uuid $id,
        public ?Uuid $gameId,
        public ?array $log,
        public ?Uuid $createdBy,
        public \DateTimeImmutable $createdAt,
        public ?int $turns = null,
        public ?int $winnerAmberObtained = null,
        public ?int $winnerAmberStolen = null,
        public ?int $winnerCardsPlayed = null,
        public ?int $winnerCardsDrawn = null,
        public ?int $winnerCardsDiscarded = null,
        public ?int $winnerKeysForged = null,
        public ?int $winnerFights = null,
        public ?int $winnerReaps = null,
        public ?int $winnerExtraTurns = null,
        public ?int $loserAmberObtained = null,
        public ?int $loserAmberStolen = null,
        public ?int $loserCardsPlayed = null,
        public ?int $loserCardsDrawn = null,
        public ?int $loserCardsDiscarded = null,
        public ?int $loserKeysForged = null,
        public ?int $loserFights = null,
        public ?int $loserReaps = null,
        public ?int $loserExtraTurns = null,
    ) {}

    public function jsonSerialize(): array
    {
        return [
            'id' => $this->id->value(),
            'gameId' => $this->gameId?->value(),
            'log' => $this->log,
            'createdBy' => $this->createdBy?->value(),
            'createdAt' => $this->createdAt->format('Y-m-d'),
            'turns' => $this->turns,
            'winnerAmberObtained' => $this->winnerAmberObtained,
            'winnerAmberStolen' => $this->winnerAmberStolen,
            'winnerCardsPlayed' => $this->winnerCardsPlayed,
            'winnerCardsDrawn' => $this->winnerCardsDrawn,
            'winnerCardsDiscarded' => $this->winnerCardsDiscarded,
            'winnerKeysForged' => $this->winnerKeysForged,
            'winnerFights' => $this->winnerFights,
            'winnerReaps' => $this->winnerReaps,
            'winnerExtraTurns' => $this->winnerExtraTurns,
            'loserAmberObtained' => $this->loserAmberObtained,
            'loserAmberStolen' => $this->loserAmberStolen,
            'loserCardsPlayed' => $this->loserCardsPlayed,
            'loserCardsDrawn' => $this->loserCardsDrawn,
            'loserCardsDiscarded' => $this->loserCardsDiscarded,
            'loserKeysForged' => $this->loserKeysForged,
            'loserFights' => $this->loserFights,
            'loserReaps' => $this->loserReaps,
            'loserExtraTurns' => $this->loserExtraTurns,
        ];
    }
}
