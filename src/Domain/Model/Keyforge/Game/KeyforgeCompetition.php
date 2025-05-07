<?php declare(strict_types=1);

namespace AdnanMula\Cards\Domain\Model\Keyforge\Game;

use AdnanMula\Cards\Domain\Model\Shared\ValueObject\CompetitionVisibility;
use AdnanMula\Cards\Domain\Model\Shared\ValueObject\Uuid;
use AdnanMula\Tournament\Classification\Classification;
use AdnanMula\Tournament\Fixture\Fixtures;
use AdnanMula\Tournament\Tournament;
use AdnanMula\Tournament\TournamentType;
use AdnanMula\Tournament\User;

final class KeyforgeCompetition extends Tournament
{
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
        foreach (array_merge($admins, $players) as $user) {
            if (false === $user instanceof User) {
                throw new \InvalidArgumentException('Admins and players must be an instance of ' . User::class);
            }
        }

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
