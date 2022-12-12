<?php declare(strict_types=1);

namespace AdnanMula\Cards\Domain\Model\Gwent;

use AdnanMula\Cards\Domain\Model\Shared\ValueObject\Uuid;
use AdnanMula\Cards\Infrastructure\Criteria\Criteria;

interface GwentDeckRepository
{
    /** @return array<GwentDeck> */
    public function search(Criteria $criteria): array;

    public function count(Criteria $criteria): int;

    public function byId(Uuid $id): ?GwentDeck;

    public function archetypeById(Uuid $id): ?GwentArchetype;

    /** @return array<GwentDeck> */
    public function byIds(Uuid ...$id): array;

    public function save(GwentDeck $deck): void;

    public function saveArchetype(GwentArchetype $archetype): void;
}
