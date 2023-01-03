<?php declare(strict_types=1);

namespace AdnanMula\Cards\Domain\Model\Keyforge;

use AdnanMula\Cards\Domain\Model\Shared\ValueObject\Uuid;
use AdnanMula\Cards\Infrastructure\Criteria\Criteria;

interface KeyforgeCompetitionRepository
{
    /** @return array<KeyforgeCompetition> */
    public function search(Criteria $criteria): array;

    public function count(Criteria $criteria): int;

    public function byId(Uuid $id): ?KeyforgeCompetition;

    public function byReference(string $reference): ?KeyforgeCompetition;

    public function save(KeyforgeCompetition $competition): void;

    /** @return array<KeyforgeCompetitionFixture> */
    public function fixtures(Uuid $competitionId): array;

    public function saveFixture(KeyforgeCompetitionFixture $fixture): void;
}
