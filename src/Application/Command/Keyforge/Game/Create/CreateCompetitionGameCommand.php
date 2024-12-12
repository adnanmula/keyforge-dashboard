<?php declare(strict_types=1);

namespace AdnanMula\Cards\Application\Command\Keyforge\Game\Create;

use AdnanMula\Cards\Domain\Model\Keyforge\Game\ValueObject\KeyforgeCompetition;
use AdnanMula\Cards\Domain\Model\Shared\ValueObject\Uuid;
use Assert\Assert;

final readonly class CreateCompetitionGameCommand
{
    private(set) Uuid $winner;
    private(set) string $winnerDeck;
    private(set) int $winnerChains;
    private(set) Uuid $loser;
    private(set) string $loserDeck;
    private(set) int $loserChains;
    private(set) int $loserScore;
    private(set) ?Uuid $firstTurn;
    private(set) \DateTimeImmutable $date;
    private(set) KeyforgeCompetition $competition;
    private(set) string $notes;
    private(set) Uuid $fixtureId;

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
        $fixtureId,
    ) {
        Assert::lazy()
            ->that($winner, 'winner')->uuid()
            ->that($winnerDeck, 'winnerDeck')->string()->notBlank()
            ->that($winnerChains, 'winnerChains')->integerish()->min(0)
            ->that($loser, 'loser')->uuid()
            ->that($loserDeck, 'loserDeck')->string()->notBlank()
            ->that($loserChains, 'loserChains')->integerish()->min(0)
            ->that($loserScore, 'loserScore')->integerish()->min(0)->max(2)
            ->that($firstTurn, 'firstTurn')->nullOr()->uuid()
            ->that($date, 'date')->date('Y-m-d')
            ->that($competition, 'competition')->inArray(KeyforgeCompetition::values())
            ->that($notes, 'notes')->string()->maxLength(512)
            ->that($fixtureId, 'competitionId')->uuid()
            ->verifyNow();

        if (null !== $firstTurn && $firstTurn !== $winner && $firstTurn !== $loser) {
            throw new \InvalidArgumentException('First player is not in game');
        }

        $this->winner = Uuid::from($winner);
        $this->winnerDeck = $winnerDeck;
        $this->winnerChains = (int) $winnerChains;
        $this->loser = Uuid::from($loser);
        $this->loserDeck = $loserDeck;
        $this->loserChains = (int) $loserChains;
        $this->loserScore = (int) $loserScore;
        $this->firstTurn = null === $firstTurn ? null : Uuid::from($firstTurn);
        $this->date = new \DateTimeImmutable($date);
        $this->competition = KeyforgeCompetition::fromName($competition);
        $this->notes = $notes;
        $this->fixtureId = Uuid::from($fixtureId);
    }
}
