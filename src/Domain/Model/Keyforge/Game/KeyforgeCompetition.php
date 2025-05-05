<?php declare(strict_types=1);

namespace AdnanMula\Cards\Domain\Model\Keyforge\Game;

use AdnanMula\Cards\Domain\Model\Shared\ValueObject\CompetitionVisibility;
use AdnanMula\Cards\Domain\Model\Shared\ValueObject\Uuid;
use AdnanMula\Tournament\Classification\Classification;
use AdnanMula\Tournament\Fixture\Fixtures;
use AdnanMula\Tournament\Tournament;
use AdnanMula\Tournament\TournamentType;

final class KeyforgeCompetition extends Tournament
{
    /**
     * @param array<Uuid> $admins
     * @param array<Uuid> $players
     */
    public function __construct(
        private(set) readonly Uuid $id,
        string $name,
        string $description,
        TournamentType $type,
        array $admins,
        array $players,
        \DateTimeImmutable $createdAt,
        ?\DateTimeImmutable $startedAt,
        ?\DateTimeImmutable $finishedAt,
        private(set) readonly CompetitionVisibility $visibility,
        private(set) ?Uuid $winner,
        ?Fixtures $fixtures,
        Classification $classification,
    ) {
        $admins = array_map(static fn (Uuid $id): string => $id->value(), $admins);
        $players = array_map(static fn (Uuid $id): string => $id->value(), $players);

        parent::__construct($name, $description, $type, $admins, $players, $createdAt, $startedAt, $finishedAt, $fixtures, $classification);
    }

    public function updateWinner(Uuid $winnerId): self
    {
        $this->winner = $winnerId;

        return $this;
    }

    public function jsonSerialize(): array
    {
        return array_merge(
            parent::jsonSerialize(),
            [
                'id' => $this->id,
                'visibility' => $this->visibility,
                'winner' => $this->winner,
            ],
        );
    }
}
