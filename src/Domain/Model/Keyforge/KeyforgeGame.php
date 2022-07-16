<?php declare(strict_types=1);

namespace AdnanMula\Cards\Domain\Model\Keyforge;

use AdnanMula\Cards\Domain\Model\Keyforge\ValueObject\KeyforgeGameScore;
use AdnanMula\Cards\Domain\Model\Shared\ValueObject\Uuid;

final class KeyforgeGame
{
    public function __construct(
        private Uuid $id,
        private Uuid $winner,
        private Uuid $loser,
        private Uuid $winnerDeck,
        private Uuid $loserDeck,
        private ?Uuid $firstTurn,
        private KeyforgeGameScore $score,
        private \DateTimeImmutable $date,
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
}
