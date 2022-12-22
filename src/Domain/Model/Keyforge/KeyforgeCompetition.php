<?php declare(strict_types=1);

namespace AdnanMula\Cards\Domain\Model\Keyforge;

use AdnanMula\Cards\Domain\Model\Shared\ValueObject\CompetitionType;
use AdnanMula\Cards\Domain\Model\Shared\ValueObject\Uuid;

final class KeyforgeCompetition implements \JsonSerializable
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

    public function startAt(): ?\DateTimeImmutable
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
            'id' => $this->id(),
            'reference' => $this->reference(),
            'name' => $this->name(),
            'type' => $this->type(),
            'users' => $this->users(),
            'description' => $this->description(),
            'createdAt' => $this->createdAt(),
            'startAt' => $this->startAt(),
            'finishedAt' => $this->finishedAt(),
            'winner' => $this->winner(),
        ];
    }
}
