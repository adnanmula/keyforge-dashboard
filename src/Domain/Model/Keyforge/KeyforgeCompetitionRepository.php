<?php declare(strict_types=1);

namespace AdnanMula\Cards\Domain\Model\Keyforge;

use AdnanMula\Cards\Domain\Model\Shared\ValueObject\Uuid;
use AdnanMula\Cards\Infrastructure\Criteria\Criteria;

interface KeyforgeCompetitionRepository
{
    /** @return array<KeyforgeCompetition> */
    public function search(Criteria $criteria): array;

    public function byId(Uuid $id): ?KeyforgeCompetition;

    /** @return array<KeyforgeCompetition> */
    public function byIds(Uuid ...$id): array;

    public function save(KeyforgeCompetition $competition): void;
}
