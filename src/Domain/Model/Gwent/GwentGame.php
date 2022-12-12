<?php declare(strict_types=1);

namespace AdnanMula\Cards\Domain\Model\Gwent;

use AdnanMula\Cards\Domain\Model\Gwent\ValueObject\GwentCoin;
use AdnanMula\Cards\Domain\Model\Gwent\ValueObject\GwentGameScore;
use AdnanMula\Cards\Domain\Model\Shared\ValueObject\Uuid;

final class GwentGame
{
    public function __construct(
        private Uuid $id,
        private Uuid $userId,
        private Uuid $deck,
        private Uuid $opponentDeckArchetype,
        private bool $win,
        private int $rank,
        private GwentCoin $coin,
        private GwentGameScore $score,
        private \DateTimeImmutable $date,
        private \DateTimeImmutable $createdAt,
    ) {}

    public function id(): Uuid
    {
        return $this->id;
    }

    public function userId(): Uuid
    {
        return $this->userId;
    }

    public function deck(): Uuid
    {
        return $this->deck;
    }

    public function opponentDeckArchetype(): Uuid
    {
        return $this->opponentDeckArchetype;
    }

    public function win(): bool
    {
        return $this->win;
    }

    public function rank(): int
    {
        return $this->rank;
    }

    public function coin(): GwentCoin
    {
        return $this->coin;
    }

    public function score(): GwentGameScore
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
}
