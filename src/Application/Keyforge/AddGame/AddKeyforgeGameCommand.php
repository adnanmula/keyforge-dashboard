<?php declare(strict_types=1);

namespace AdnanMula\Cards\Application\Keyforge\AddGame;

use AdnanMula\Cards\Domain\Model\Shared\ValueObject\UuidValueObject;
use Assert\Assert;

final class AddKeyforgeGameCommand
{
    private UuidValueObject $winner;
    private UuidValueObject $winnerDeck;
    private UuidValueObject $loser;
    private UuidValueObject $loserDeck;
    private int $loserScore;
    private ?UuidValueObject $firstTurn;
    private \DateTimeImmutable $date;

    public function __construct($winner, $winnerDeck, $loser, $loserDeck, $loserScore, $firstTurn, $date)
    {
        Assert::lazy()
            ->that($winner, 'winner')->uuid()
            ->that($winnerDeck, 'winnerDeck')->uuid()
            ->that($loser, 'loser')->uuid()
            ->that($loserDeck, 'loserDeck')->uuid()
            ->that($loserScore, 'loserScore')->integerish()->min(0)->max(2)
            ->that($firstTurn, 'firstTurn')->nullOr()->uuid()
            ->that($date, 'date')->date('Y-m-d H:i:s');

        $this->winner = UuidValueObject::from($winner);
        $this->winnerDeck = UuidValueObject::from($winnerDeck);
        $this->loser = UuidValueObject::from($loser);
        $this->loserDeck = UuidValueObject::from($loserDeck);
        $this->loserScore = (int) $loserScore;
        $this->firstTurn = null === $firstTurn
            ? null
            : UuidValueObject::from($firstTurn);
        $this->date = new \DateTimeImmutable($date);
    }

    public function winner(): UuidValueObject
    {
        return $this->winner;
    }

    public function winnerDeck(): UuidValueObject
    {
        return $this->winnerDeck;
    }

    public function loser(): UuidValueObject
    {
        return $this->loser;
    }

    public function loserDeck(): UuidValueObject
    {
        return $this->loserDeck;
    }

    public function loserScore(): int
    {
        return $this->loserScore;
    }

    public function firstTurn(): ?UuidValueObject
    {
        return $this->firstTurn;
    }

    public function date(): \DateTimeImmutable
    {
        return $this->date;
    }
}
