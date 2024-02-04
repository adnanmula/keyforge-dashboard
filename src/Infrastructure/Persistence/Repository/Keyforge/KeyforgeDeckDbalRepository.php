<?php declare(strict_types=1);

namespace AdnanMula\Cards\Infrastructure\Persistence\Repository\Keyforge;

use AdnanMula\Cards\Application\Service\Json;
use AdnanMula\Cards\Domain\Model\Keyforge\KeyforgeDeck;
use AdnanMula\Cards\Domain\Model\Keyforge\KeyforgeDeckRepository;
use AdnanMula\Cards\Domain\Model\Keyforge\ValueObject\KeyforgeDeckHouses;
use AdnanMula\Cards\Domain\Model\Keyforge\ValueObject\KeyforgeHouse;
use AdnanMula\Cards\Domain\Model\Keyforge\ValueObject\KeyforgeSet;
use AdnanMula\Cards\Domain\Model\Shared\ValueObject\Uuid;
use AdnanMula\Cards\Infrastructure\Persistence\Repository\DbalRepository;
use AdnanMula\Criteria\Criteria;
use AdnanMula\Criteria\DbalCriteriaAdapter;
use Doctrine\DBAL\Connection;

final class KeyforgeDeckDbalRepository extends DbalRepository implements KeyforgeDeckRepository
{
    private const TABLE = 'keyforge_decks';

    public function search(Criteria $criteria): array
    {
        $builder = $this->connection->createQueryBuilder();

        $query = $builder->select('a.*')
            ->from(self::TABLE, 'a');

        (new DbalCriteriaAdapter($builder))->execute($criteria);

        $result = $query->executeQuery()->fetchAllAssociative();

        return \array_map(fn (array $row) => $this->map($row), $result);
    }

    public function count(Criteria $criteria): int
    {
        $builder = $this->connection->createQueryBuilder();
        $query = $builder->select('COUNT(a.id)')
            ->from(self::TABLE, 'a');

        (new DbalCriteriaAdapter($builder))->execute($criteria);

        return $query->executeQuery()->fetchOne();
    }

    public function byId(Uuid $id): ?KeyforgeDeck
    {
        $result = $this->connection->createQueryBuilder()
            ->select('a.*')
            ->from(self::TABLE, 'a')
            ->where('a.id = :id')
            ->setParameter('id', $id->value())
            ->setMaxResults(1)
            ->executeQuery()
            ->fetchAssociative();

        if ([] === $result || false === $result) {
            return null;
        }

        return $this->map($result);
    }

    public function byIds(Uuid ...$ids): array
    {
        $result = $this->connection->createQueryBuilder()
            ->select('a.*')
            ->from(self::TABLE, 'a')
            ->where('a.id in (:ids)')
            ->setParameter('ids', \array_map(static fn (Uuid $id) => $id->value(), $ids), Connection::PARAM_STR_ARRAY)
            ->executeQuery()
            ->fetchAllAssociative();

        if ([] === $result || false === $result) {
            return [];
        }

        return \array_map(fn (array $deck) => $this->map($deck), $result);
    }

    public function byNames(string ...$decks): array
    {
        $result = $this->connection->createQueryBuilder()
            ->select('a.*')
            ->from(self::TABLE, 'a')
            ->where('a.name in (:decks)')
            ->setParameter('decks', $decks, Connection::PARAM_STR_ARRAY)
            ->executeQuery()
            ->fetchAllAssociative();

        if ([] === $result || false === $result) {
            return [];
        }

        return \array_map(fn (array $deck) => $this->map($deck), $result);
    }

    public function save(KeyforgeDeck $deck): void
    {
        $stmt = $this->connection->prepare(
            \sprintf(
                '
                    INSERT INTO %s (id, name, set, houses, sas, wins, losses, extra_data, owner, tags, notes, prev_sas, new_sas)
                    VALUES (:id, :name, :set, :houses, :sas, :wins, :losses, :extra_data, :owner, :tags, :notes, :prev_sas, :new_sas)
                    ON CONFLICT (id) DO UPDATE SET
                        id = :id,
                        name = :name,
                        set = :set,
                        houses = :houses,
                        sas = :sas,
                        wins = :wins,
                        losses = :losses,
                        extra_data = :extra_data,
                        owner = :owner,
                        tags = :tags,
                        notes = :notes,
                        prev_sas = :prev_sas,
                        new_sas = :new_sas
                    ',
                self::TABLE,
            ),
        );

        $stmt->bindValue(':id', $deck->id()->value());
        $stmt->bindValue(':name', $deck->name());
        $stmt->bindValue(':set', $deck->set()->name);
        $stmt->bindValue(':houses', Json::encode($deck->houses()->value()));
        $stmt->bindValue(':sas', $deck->sas());
        $stmt->bindValue(':wins', $deck->wins());
        $stmt->bindValue(':losses', $deck->losses());
        $stmt->bindValue(':extra_data', Json::encode($deck->extraData()));
        $stmt->bindValue(':owner', $deck->owner()?->value());
        $stmt->bindValue(':tags', Json::encode($deck->tags()));
        $stmt->bindValue(':notes', $deck->notes());
        $stmt->bindValue(':prev_sas', $deck->prevSas());
        $stmt->bindValue(':new_sas', $deck->newSas());

        $stmt->executeStatement();
    }

    private function map(array $deck): KeyforgeDeck
    {
        return new KeyforgeDeck(
            Uuid::from($deck['id']),
            $deck['name'],
            KeyforgeSet::from($deck['set']),
            KeyforgeDeckHouses::from(
                ...\array_map(
                    static fn (string $house) => KeyforgeHouse::from($house),
                    Json::decode($deck['houses']),
                ),
            ),
            $deck['sas'],
            $deck['wins'],
            $deck['losses'],
            Json::decode($deck['extra_data']),
            null === $deck['owner'] ? null : Uuid::from($deck['owner']),
            $deck['notes'],
            Json::decode($deck['tags']),
            $deck['prev_sas'],
            $deck['new_sas'],
        );
    }
}
