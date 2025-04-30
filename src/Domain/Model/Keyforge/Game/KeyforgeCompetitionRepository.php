<?php declare(strict_types=1);

namespace AdnanMula\Cards\Domain\Model\Keyforge\Game;

use AdnanMula\Cards\Domain\Model\Shared\ValueObject\Uuid;
use AdnanMula\Criteria\Criteria;

interface KeyforgeCompetitionRepository
{
    /** @return array<KeyforgeCompetition> */
    public function search(Criteria $criteria): array;

    public function searchOne(Criteria $criteria): ?KeyforgeCompetition;

    public function count(Criteria $criteria): int;

    public function save(KeyforgeCompetition $competition): void;

    /** @return array<KeyforgeCompetitionFixture> */
    public function fixtures(Uuid $competitionId): array;

    public function fixtureById(Uuid $id): ?KeyforgeCompetitionFixture;

    public function saveFixture(KeyforgeCompetitionFixture $fixture): void;
}
