<?php declare(strict_types=1);

namespace AdnanMula\Cards\Application\Command\Keyforge\Game\Create;

use AdnanMula\Cards\Domain\Model\Keyforge\Game\ValueObject\KeyforgeCompetition;
use Assert\Assert;

final readonly class CreateGameCommand
{
    private(set) string $winner;
    private(set) string $winnerDeck;
    private(set) int $winnerChains;
    private(set) string $loser;
    private(set) string $loserDeck;
    private(set) int $loserChains;
    private(set) int $loserScore;
    private(set) ?string $firstTurn;
    private(set) \DateTimeImmutable $date;
    private(set) KeyforgeCompetition $competition;
    private(set) string $notes;

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
}
