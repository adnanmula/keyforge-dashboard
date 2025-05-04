<?php declare(strict_types=1);

namespace AdnanMula\Cards\Domain\Model\Keyforge\Game;

use AdnanMula\Cards\Domain\Model\Shared\ValueObject\Uuid;
use AdnanMula\Tournament\Fixture\Fixture;
use AdnanMula\Tournament\Fixture\FixtureType;

final class KeyforgeCompetitionFixture extends Fixture
{
    public function __construct(
        private(set) Uuid $id,
        private(set) Uuid $competitionId,
        private(set) ?Uuid $winner,
        private(set) array $games,
        $reference,
        $players,
        FixtureType $type,
        int $position,
        \DateTimeImmutable $createdAt,
        ?\DateTimeImmutable $playedAt,
    ) {
        parent::__construct($reference, $players, $type, $position, $createdAt, $playedAt);
    }

    /** @return array<Uuid> */
    public function games(): array
    {
        return $this->games;
    }

    public function updateWinner(?Uuid $id): void
    {
        $this->winner = $id;
    }

    public function updateGames(Uuid ...$games): void
    {
        $this->games = $games;
    }

    public function addGame(Uuid $id): void
    {
        $this->games[] = $id;
    }

    public function jsonSerialize(): array
    {
        return array_merge(
            parent::jsonSerialize(),
            [
                'id' => $this->id,
                'competitionId' => $this->competitionId,
                'winner' => $this->winner,
                'games' => $this->games,
            ],
        );
    }
}
