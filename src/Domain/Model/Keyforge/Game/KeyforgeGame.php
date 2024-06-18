<?php declare(strict_types=1);

namespace AdnanMula\Cards\Domain\Model\Keyforge\Game;

use AdnanMula\Cards\Domain\Model\Keyforge\Game\ValueObject\KeyforgeCompetition;
use AdnanMula\Cards\Domain\Model\Keyforge\Game\ValueObject\KeyforgeGameScore;
use AdnanMula\Cards\Domain\Model\Shared\ValueObject\Uuid;

final class KeyforgeGame implements \JsonSerializable
{
    public function __construct(
        private Uuid $id,
        private Uuid $winner,
        private Uuid $loser,
        private Uuid $winnerDeck,
        private Uuid $loserDeck,
        private int $winnerChains,
        private int $loserChains,
        private ?Uuid $firstTurn,
        private KeyforgeGameScore $score,
        private \DateTimeImmutable $date,
        private \DateTimeImmutable $createdAt,
        private KeyforgeCompetition $competition,
        private string $notes,
        private bool $approved,
        private ?Uuid $createdBy,
    ) {}

    public function id(): Uuid
    {
        return $this->id;
    }

    public function winner(): Uuid
    {
        return $this->winner;
    }

    public function loser(): Uuid
    {
        return $this->loser;
    }

    public function winnerDeck(): Uuid
    {
        return $this->winnerDeck;
    }

    public function loserDeck(): Uuid
    {
        return $this->loserDeck;
    }

    public function winnerChains(): int
    {
        return $this->winnerChains;
    }

    public function loserChains(): int
    {
        return $this->loserChains;
    }

    public function firstTurn(): ?Uuid
    {
        return $this->firstTurn;
    }

    public function score(): KeyforgeGameScore
    {
        return $this->score;
    }

    public function date(): \DateTimeImmutable
    {
        return $this->date;
    }

    public function createdAt(): \DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function competition(): KeyforgeCompetition
    {
        return $this->competition;
    }

    public function notes(): string
    {
        return $this->notes;
    }

    public function approved(): bool
    {
        return $this->approved;
    }

    public function createdBy(): ?Uuid
    {
        return $this->createdBy;
    }

    public function isSoloPlay(): bool
    {
        return $this->winner->equalTo($this->loser);
    }

    public function isMirror(): bool
    {
        return $this->winnerDeck->equalTo($this->loserDeck);
    }

    public function jsonSerialize(): array
    {
        return [
            'id' => $this->id()->value(),
            'winner' => $this->winner()->value(),
            'loser' => $this->loser()->value(),
            'winnerDeck' => $this->winnerDeck()->value(),
            'loserDeck' => $this->loserDeck()->value(),
            'winnerChains' => $this->winnerChains(),
            'loserChains' => $this->loserChains(),
            'firstTurn' => $this->firstTurn()?->value(),
            'score' => $this->score()->jsonSerialize(),
            'date' => $this->date()->format('Y-m-d'),
            'createdAt' => $this->createdAt()->format('Y-m-d'),
            'competition' => $this->competition()->name,
            'notes' => $this->notes(),
            'approved' => $this->approved(),
            'created_by' => $this->createdBy()?->value(),
        ];
    }
}
