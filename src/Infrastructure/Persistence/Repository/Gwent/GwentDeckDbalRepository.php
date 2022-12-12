<?php declare(strict_types=1);

namespace AdnanMula\Cards\Infrastructure\Persistence\Repository\Gwent;

use AdnanMula\Cards\Domain\Model\Gwent\GwentArchetype;
use AdnanMula\Cards\Domain\Model\Gwent\GwentDeck;
use AdnanMula\Cards\Domain\Model\Gwent\GwentDeckRepository;
use AdnanMula\Cards\Domain\Model\Gwent\ValueObject\GwentDeckType;
use AdnanMula\Cards\Domain\Model\Gwent\ValueObject\GwentFaction;
use AdnanMula\Cards\Domain\Model\Shared\ValueObject\Uuid;
use AdnanMula\Cards\Infrastructure\Criteria\Criteria;
use AdnanMula\Cards\Infrastructure\Criteria\DbalCriteriaAdapter;
use AdnanMula\Cards\Infrastructure\Persistence\Repository\DbalRepository;
use Doctrine\DBAL\Connection;

final class GwentDeckDbalRepository extends DbalRepository implements GwentDeckRepository
{
    private const TABLE = 'gwent_decks';
    private const TABLE_ARCHETYPE = 'gwent_archetypes';

    public function search(Criteria $criteria): array
    {
        $builder = $this->connection->createQueryBuilder();

        $query = $builder->select('a.id, a.name, a.faction, a.archetype, a.type, a.wins, a.losses')
            ->from(self::TABLE, 'a');

        (new DbalCriteriaAdapter($builder))->execute($criteria);

        $result = $query->execute()->fetchAllAssociative();

        return \array_map(fn (array $row) => $this->map($row), $result);
    }

    public function count(Criteria $criteria): int
    {
        $builder = $this->connection->createQueryBuilder();
        $query = $builder->select('COUNT(a.id)')
            ->from(self::TABLE, 'a');

        (new DbalCriteriaAdapter($builder))->execute($criteria);

        return $query->execute()->fetchOne();
    }

    public function byId(Uuid $id): ?GwentDeck
    {
        $result = $this->connection->createQueryBuilder()
            ->select('a.id, a.name, a.faction, a.archetype, a.type, a.wins, a.losses')
            ->from(self::TABLE, 'a')
            ->where('a.id = :id')
            ->setParameter('id', $id->value())
            ->setMaxResults(1)
            ->execute()
            ->fetchAssociative();

        if ([] === $result || false === $result) {
            return null;
        }

        return $this->map($result);
    }

    public function archetypeById(Uuid $id): ?GwentArchetype
    {
        $result = $this->connection->createQueryBuilder()
            ->select('a.id, a.name, a.faction')
            ->from(self::TABLE_ARCHETYPE, 'a')
            ->where('a.id = :id')
            ->setParameter('id', $id->value())
            ->setMaxResults(1)
            ->execute()
            ->fetchAssociative();

        if ([] === $result || false === $result) {
            return null;
        }

        return $this->mapArchetype($result);
    }


    public function byIds(Uuid ...$ids): array
    {
        $result = $this->connection->createQueryBuilder()
            ->select('a.id, a.name, a.faction, a.archetype, a.type, a.wins, a.losses')
            ->from(self::TABLE, 'a')
            ->where('a.id in (:ids)')
            ->setParameter('ids', \array_map(static fn (Uuid $id) => $id->value(), $ids), Connection::PARAM_STR_ARRAY)
            ->execute()
            ->fetchAllAssociative();

        if ([] === $result || false === $result) {
            return [];
        }

        return \array_map(fn (array $deck) => $this->map($deck), $result);
    }

    public function save(GwentDeck $deck): void
    {
        $stmt = $this->connection->prepare(
            \sprintf('
                INSERT INTO %s (id, name, faction, archetype, type, wins, losses)
                VALUES (:id, :name, :faction, :archetype, :type, :wins, :losses)
                ON CONFLICT (id) DO UPDATE SET
                    id = :id,
                    name = :name,
                    faction = :faction,
                    archetype = :archetype,
                    type = :type,
                    wins = :wins,
                    losses = :losses
                ',
                self::TABLE,
            ),
        );

        $stmt->bindValue(':id', $deck->id()->value());
        $stmt->bindValue(':name', $deck->name());
        $stmt->bindValue(':faction', $deck->faction()->name);
        $stmt->bindValue(':archetype', $deck->archetype()?->value());
        $stmt->bindValue(':type', $deck->type()->name);
        $stmt->bindValue(':wins', $deck->wins());
        $stmt->bindValue(':losses', $deck->losses());

        $stmt->execute();
    }

    public function saveArchetype(GwentArchetype $archetype): void
    {
    }

    private function map(array $result): GwentDeck
    {
        return new GwentDeck(
            Uuid::from($result['id']),
            GwentFaction::from($result['faction']),
            $result['archetype'] === null ? null : Uuid::from($result['archetype']),
            GwentDeckType::from($result['type']),
            $result['name'],
            $result['wins'],
            $result['losses'],
        );
    }

    private function mapArchetype(array $result): GwentArchetype
    {
        return new GwentArchetype(
            Uuid::from($result['id']),
            GwentFaction::from($result['faction']),
            $result['name']
        );
    }
}
