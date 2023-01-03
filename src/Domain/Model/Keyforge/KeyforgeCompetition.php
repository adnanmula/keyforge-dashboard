<?php declare(strict_types=1);

namespace AdnanMula\Cards\Domain\Model\Keyforge;

use AdnanMula\Cards\Domain\Model\Shared\ValueObject\CompetitionType;
use AdnanMula\Cards\Domain\Model\Shared\ValueObject\Uuid;

final readonly class KeyforgeCompetition implements \JsonSerializable
{
    public function __construct(
        private Uuid $id,
        private string $reference,
        private string $name,
        private CompetitionType $type,
        private array $users,
        private string $description,
        private \DateTimeImmutable $createdAt,
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

    public function jsonSerialize(): array
    {
        return [
            'id' => $this->id()->value(),
            'reference' => $this->reference(),
            'name' => $this->name(),
            'type' => $this->type()->name,
            'users' => $this->users(),
            'description' => $this->description(),
            'createdAt' => $this->createdAt()?->format(\DateTimeInterface::ATOM),
            'startedAt' => $this->startedAt()?->format('Y-m-d'),
            'finishedAt' => $this->finishedAt()?->format('Y-m-d'),
            'winner' => $this->winner()?->value(),
        ];
    }
}
