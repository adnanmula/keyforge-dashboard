<?php declare(strict_types=1);

namespace AdnanMula\Cards\Application\Command\Keyforge\Game\Create;

use AdnanMula\Cards\Domain\Model\Keyforge\Game\ValueObject\KeyforgeCompetition;
use Assert\Assert;

final class CreateGameCommand
{
    private string $winner;
    private string $winnerDeck;
    private int $winnerChains;
    private string $loser;
    private string $loserDeck;
    private int $loserChains;
    private int $loserScore;
    private ?string $firstTurn;
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
            ->that($winner, 'winner')->string()
            ->that($winnerDeck, 'winnerDeck')->string()->notBlank()
            ->that($winnerChains, 'winnerChains')->integerish()->min(0)
            ->that($loser, 'loser')->string()
            ->that($loserDeck, 'loserDeck')->string()->notBlank()
            ->that($loserChains, 'loserChains')->integerish()->min(0)
            ->that($loserScore, 'loserScore')->integerish()->min(0)->max(2)
            ->that($firstTurn, 'firstTurn')->nullOr()->string()
            ->that($date, 'date')->date('Y-m-d')
            ->that($competition, 'competition')->inArray(KeyforgeCompetition::values())
            ->that($notes, 'notes')->string()->maxLength(512)
            ->verifyNow();

        $this->winner = $winner;
        $this->winnerDeck = $winnerDeck;
        $this->winnerChains = (int) $winnerChains;
        $this->loser = $loser;
        $this->loserDeck = $loserDeck;
        $this->loserChains = (int) $loserChains;
        $this->loserScore = (int) $loserScore;
        $this->firstTurn = $firstTurn;
        $this->date = new \DateTimeImmutable($date);
        $this->competition = KeyforgeCompetition::fromName($competition);
        $this->notes = $notes;
    }

    public function winner(): string
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

    public function loser(): string
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

    public function firstTurn(): ?string
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
