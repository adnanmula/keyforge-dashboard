<?php declare(strict_types=1);

namespace AdnanMula\Cards\Domain\Model\Keyforge;

use AdnanMula\Cards\Domain\Model\Keyforge\ValueObject\KeyforgeGameScore;
use AdnanMula\Cards\Domain\Model\Shared\ValueObject\UuidValueObject;

final class KeyforgeGame
{
    private const MODEL_NAME = 'keyforge_game';

    public function __construct(
        private UuidValueObject $id,
        private UuidValueObject $winner,
        private UuidValueObject $loser,
        private UuidValueObject $winnerDeck,
        private UuidValueObject $loserDeck,
        private UuidValueObject $firstTurn,
        private KeyforgeGameScore $score,
        private \DateTimeImmutable $date
    ) {}

    public function id(): UuidValueObject
    {
        return $this->id;
    }

    public function winner(): UuidValueObject
    {
        return $this->winner;
    }

    public function loser(): UuidValueObject
    {
        return $this->loser;
    }

    public function winnerDeck(): UuidValueObject
    {
        return $this->winnerDeck;
    }

    public function loserDeck(): UuidValueObject
    {
        return $this->loserDeck;
    }

    public function firstTurn(): UuidValueObject
    {
        return $this->firstTurn;
    }

    public function score(): KeyforgeGameScore
    {
        return $this->score;
    }

    public static function modelName(): string
    {
        return self::MODEL_NAME;
    }

    public function date(): \DateTimeImmutable
    {
        return $this->date;
    }
}
