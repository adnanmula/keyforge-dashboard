<?php declare(strict_types=1);

namespace AdnanMula\Cards\Application\Command\Keyforge\Game\Create;

use AdnanMula\Cards\Domain\Model\Keyforge\ValueObject\KeyforgeCompetition;
use AdnanMula\Cards\Domain\Model\Shared\ValueObject\Uuid;
use Assert\Assert;

final class CreateGameCommand
{
    private Uuid $winner;
    private string $winnerDeck;
    private int $winnerChains;
    private Uuid $loser;
    private string $loserDeck;
    private int $loserChains;
    private int $loserScore;
    private ?Uuid $firstTurn;
    private \DateTimeImmutable $date;
    private KeyforgeCompetition $competition;
    private string $notes;

    public function __construct(
        $winner,
        $winnerDeck,
        $winnerChains,
        $loser,
        $loserDeck,
        $loserChains,
        $loserScore,
        $firstTurn,
        $date,
        $competition,
        $notes,
    ) {
        Assert::lazy()
            ->that($winner, 'winner')->uuid()
            ->that($winnerDeck, 'winnerDeck')->string()->notBlank()->notEq($loserDeck)
            ->that($winnerChains, 'winnerChains')->integerish()->min(0)
            ->that($loser, 'loser')->uuid()
            ->that($loserDeck, 'loserDeck')->string()->notBlank()->notEq($winnerDeck)
            ->that($loserChains, 'loserChains')->integerish()->min(0)
            ->that($loserScore, 'loserScore')->integerish()->min(0)->max(2)
            ->that($firstTurn, 'firstTurn')->nullOr()->uuid()
            ->that($date, 'date')->date('Y-m-d H:i:s')
            ->that($competition, 'competition')->inArray(KeyforgeCompetition::cases())
            ->that($notes, 'notes')->string()->maxLength(512);

        $this->winner = Uuid::from($winner);
        $this->winnerDeck = $winnerDeck;
        $this->winnerChains = (int) $winnerChains;
        $this->loser = Uuid::from($loser);
        $this->loserDeck = $loserDeck;
        $this->loserChains = (int) $loserChains;
        $this->loserScore = (int) $loserScore;
        $this->firstTurn = null === $firstTurn
            ? null
            : Uuid::from($firstTurn);
        $this->date = new \DateTimeImmutable($date);
        $this->competition = KeyforgeCompetition::fromName($competition);
        $this->notes = $notes;
    }

    public function winner(): Uuid
    {
        return $this->winner;
    }

    public function winnerDeck(): string
    {
        return $this->winnerDeck;
    }

    public function winnerChains(): int
    {
        return $this->winnerChains;
    }

    public function loser(): Uuid
    {
        return $this->loser;
    }

    public function loserDeck(): string
    {
        return $this->loserDeck;
    }

    public function loserChains(): int
    {
        return $this->loserChains;
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

    public function competition(): KeyforgeCompetition
    {
        return $this->competition;
    }

    public function notes(): string
    {
        return $this->notes;
    }
}
