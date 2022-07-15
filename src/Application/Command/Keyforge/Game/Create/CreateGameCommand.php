<?php declare(strict_types=1);

namespace AdnanMula\Cards\Application\Command\Keyforge\Game\Create;

use AdnanMula\Cards\Domain\Model\Shared\ValueObject\Uuid;
use Assert\Assert;

final class CreateGameCommand
{
    private Uuid $winner;
    private string $winnerDeck;
    private Uuid $loser;
    private string $loserDeck;
    private int $loserScore;
    private ?Uuid $firstTurn;
    private \DateTimeImmutable $date;

    public function __construct($winner, $winnerDeck, $loser, $loserDeck, $loserScore, $firstTurn, $date)
    {
        Assert::lazy()
            ->that($winner, 'winner')->uuid()
            ->that($winnerDeck, 'winnerDeck')->string()->notBlank()->notEq($loserDeck)
            ->that($loser, 'loser')->uuid()
            ->that($loserDeck, 'loserDeck')->string()->notBlank()->notEq($winnerDeck)
            ->that($loserScore, 'loserScore')->integerish()->min(0)->max(2)
            ->that($firstTurn, 'firstTurn')->nullOr()->uuid()
            ->that($date, 'date')->date('Y-m-d H:i:s');

        $this->winner = Uuid::from($winner);
        $this->winnerDeck = $winnerDeck;
        $this->loser = Uuid::from($loser);
        $this->loserDeck = $loserDeck;
        $this->loserScore = (int) $loserScore;
        $this->firstTurn = null === $firstTurn
            ? null
            : Uuid::from($firstTurn);
        $this->date = new \DateTimeImmutable($date);
    }

    public function winner(): Uuid
    {
        return $this->winner;
    }

    public function winnerDeck(): string
    {
        return $this->winnerDeck;
    }

    public function loser(): Uuid
    {
        return $this->loser;
    }

    public function loserDeck(): string
    {
        return $this->loserDeck;
    }

    public function loserScore(): int
    {
        return $this->loserScore;
    }

    public function firstTurn(): ?Uuid
    {
        return $this->firstTurn;
    }

    public function date(): \DateTimeImmutable
    {
        return $this->date;
    }
}
