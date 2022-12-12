<?php declare(strict_types=1);

namespace AdnanMula\Cards\Infrastructure\Persistence\Repository\Gwent;

use AdnanMula\Cards\Domain\Model\Gwent\GwentArchetype;
use AdnanMula\Cards\Domain\Model\Gwent\GwentDeck;
use AdnanMula\Cards\Domain\Model\Gwent\GwentDeckRepository;
use AdnanMula\Cards\Domain\Model\Gwent\GwentGame;
use AdnanMula\Cards\Domain\Model\Gwent\GwentGameRepository;
use AdnanMula\Cards\Domain\Model\Gwent\ValueObject\GwentDeckType;
use AdnanMula\Cards\Domain\Model\Gwent\ValueObject\GwentFaction;
use AdnanMula\Cards\Domain\Model\Shared\ValueObject\Uuid;
use AdnanMula\Cards\Infrastructure\Criteria\Criteria;
use AdnanMula\Cards\Infrastructure\Criteria\DbalCriteriaAdapter;
use AdnanMula\Cards\Infrastructure\Persistence\Repository\DbalRepository;
use Doctrine\DBAL\Connection;

final class GwentGameDbalRepository extends DbalRepository implements GwentGameRepository
{
    public function search(Criteria $criteria): array
    {
        return [];
    }

    public function count(Criteria $criteria): int
    {
        return 0;
    }

    public function save(GwentGame $game): void
    {
        // TODO: Implement save() method.
    }
}
