<?php declare(strict_types=1);

namespace AdnanMula\Cards\Domain\Model\Keyforge\Game;

use AdnanMula\Cards\Domain\Model\Shared\ValueObject\CompetitionType;
use AdnanMula\Cards\Domain\Model\Shared\ValueObject\Uuid;

final class KeyforgeCompetition implements \JsonSerializable
{
    public function __construct(
        private readonly Uuid $id,
        private readonly string $reference,
        private readonly string $name,
        private readonly CompetitionType $type,
        private readonly array $users,
        private readonly string $description,
        private readonly \DateTimeImmutable $createdAt,
        private ?\DateTimeImmutable $startAt,
        private ?\DateTimeImmutable $finishedAt,
        private ?Uuid $winner,
    ) {}

    public function id(): Uuid
    {
        return $this->id;
    }

    public function reference(): string
    {
        return $this->reference;
    }

    public function name(): string
    {
        return $this->name;
    }

    public function type(): CompetitionType
    {
        return $this->type;
    }

    public function users(): array
    {
        return $this->users;
    }

    public function description(): string
    {
        return $this->description;
    }

    public function createdAt(): \DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function startedAt(): ?\DateTimeImmutable
    {
        return $this->startAt;
    }

    public function finishedAt(): ?\DateTimeImmutable
    {
        return $this->finishedAt;
    }

    public function winner(): ?Uuid
    {
        return $this->winner;
    }

    public function updateStartDate(\DateTimeImmutable $date): self
    {
        $this->startAt = $date;

        return $this;
    }

    public function updateFinishDate(\DateTimeImmutable $date): self
    {
        $this->finishedAt = $date;

        return $this;
    }

    public function updateWinner(Uuid $winnerId): self
    {
        $this->winner = $winnerId;

        return $this;
    }

    public function jsonSerialize(): array
    {
        return [
            'id' => $this->id()->value(),
            'reference' => $this->reference(),
            'name' => $this->name(),
            'type' => $this->type()->name,
            'users' => \array_map(static fn (Uuid $id): string => $id->value(), $this->users()),
            'description' => $this->description(),
            'createdAt' => $this->createdAt()->format(\DateTimeInterface::ATOM),
            'startedAt' => $this->startedAt()?->format('Y-m-d'),
            'finishedAt' => $this->finishedAt()?->format('Y-m-d'),
            'winner' => $this->winner()?->value(),
        ];
    }
}
