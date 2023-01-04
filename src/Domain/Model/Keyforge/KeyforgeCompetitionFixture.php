<?php declare(strict_types=1);

namespace AdnanMula\Cards\Domain\Model\Keyforge;

use AdnanMula\Cards\Domain\Model\Shared\ValueObject\CompetitionFixtureType;
use AdnanMula\Cards\Domain\Model\Shared\ValueObject\Uuid;

final class KeyforgeCompetitionFixture implements \JsonSerializable
{
    public function __construct(
        private Uuid $id,
        private Uuid $competitionId,
        private string $reference,
        private array $users,
        private CompetitionFixtureType $type,
        private int $position,
        private \DateTimeImmutable $createdAt,
        private ?\DateTimeImmutable $playedAt,
        private ?Uuid $winner,
        private ?Uuid $game,
    ) {}

    public function id(): Uuid
    {
        return $this->id;
    }

    public function competitionId(): Uuid
    {
        return $this->competitionId;
    }

    public function reference(): string
    {
        return $this->reference;
    }

    public function users(): array
    {
        return $this->users;
    }

    public function type(): CompetitionFixtureType
    {
        return $this->type;
    }

    public function position(): int
    {
        return $this->position;
    }

    public function createdAt(): \DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function playedAt(): ?\DateTimeImmutable
    {
        return $this->playedAt;
    }

    public function winner(): ?Uuid
    {
        return $this->winner;
    }

    public function game(): ?Uuid
    {
        return $this->game;
    }

    public function updatePlayedAt(\DateTimeImmutable $at): void
    {
        $this->playedAt = $at;
    }

    public function updateWinner(Uuid $id): void
    {
        $this->winner = $id;
    }

    public function updateGame(Uuid $id): void
    {
        $this->game = $id;
    }

    public function jsonSerialize(): array
    {
        return [
            'id' => $this->id()?->value(),
            'competition_id' => $this->competitionId()?->value(),
            'reference' => $this->reference(),
            'users' => \array_map(static fn (Uuid $id): string => $id->value(), $this->users()),
            'type' => $this->type()->name,
            'position' => $this->position(),
            'createdAt' => $this->createdAt()?->format(\DateTimeInterface::ATOM),
            'playedAt' => $this->playedAt()?->format('Y-m-d'),
            'winner' => $this->winner()?->value(),
            'game' => $this->game()?->value(),
        ];
    }
}
