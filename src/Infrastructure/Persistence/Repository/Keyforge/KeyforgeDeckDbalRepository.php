<?php declare(strict_types=1);

namespace AdnanMula\Cards\Infrastructure\Persistence\Repository\Keyforge;

use AdnanMula\Cards\Application\Service\Json;
use AdnanMula\Cards\Domain\Model\Keyforge\KeyforgeDeck;
use AdnanMula\Cards\Domain\Model\Keyforge\KeyforgeDeckRepository;
use AdnanMula\Cards\Domain\Model\Keyforge\ValueObject\KeyforgeDeckHouses;
use AdnanMula\Cards\Domain\Model\Keyforge\ValueObject\KeyforgeHouse;
use AdnanMula\Cards\Domain\Model\Keyforge\ValueObject\KeyforgeSet;
use AdnanMula\Cards\Domain\Model\Shared\ValueObject\Uuid;
use AdnanMula\Cards\Infrastructure\Criteria\Criteria;
use AdnanMula\Cards\Infrastructure\Criteria\DbalCriteriaAdapter;
use AdnanMula\Cards\Infrastructure\Persistence\Repository\DbalRepository;
use Doctrine\DBAL\Connection;

final class KeyforgeDeckDbalRepository extends DbalRepository implements KeyforgeDeckRepository
{
    private const TABLE = 'keyforge_decks';
    private const TABLE_TAG_RELATION = 'keyforge_deck_tags';

    private const MAPPING = [
        'id' => 'a.id',
        'name' => 'a.name',
        'set' => 'a.set',
        'houses' => 'a.houses',
        'sas' => 'a.sas',
        'wins' => 'a.wins',
        'losses' => 'a.losses',
        'extra_data' => 'a.extra_data',
        'owner' => 'a.owner',
        'tags' => 'b.id',
    ];

    public function search(Criteria $criteria): array
    {
        $builder = $this->connection->createQueryBuilder();

        $query = $builder->select('a.id, a.name, a.set, a.houses, a.sas, a.wins, a.losses, a.extra_data, a.owner')
            ->addSelect('string_agg(b.id::varchar, \',\') as tags')
            ->from(self::TABLE, 'a')
            ->leftJoin('a', self::TABLE_TAG_RELATION, 'b', 'a.id = b.deck_id')
            ->groupBy('a.id');

        (new DbalCriteriaAdapter($builder, self::MAPPING))->execute($criteria);

        $result = $query->executeQuery()->fetchAllAssociative();

        return \array_map(fn (array $row) => $this->map($row), $result);
    }

    public function count(Criteria $criteria): int
    {
        $builder = $this->connection->createQueryBuilder();
        $query = $builder->select('COUNT(a.id)')
            ->from(self::TABLE, 'a')
            ->leftJoin('a', self::TABLE_TAG_RELATION, 'b', 'a.id = b.deck_id');

        (new DbalCriteriaAdapter($builder, self::MAPPING))->execute($criteria);

        return $query->executeQuery()->fetchOne();
    }

    public function byId(Uuid $id): ?KeyforgeDeck
    {
        $result = $this->connection->createQueryBuilder()
            ->select('a.id, a.name, a.set, a.houses, a.sas, a.wins, a.losses, a.extra_data, a.owner')
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

    public function byIds(Uuid ...$ids): array
    {
        $result = $this->connection->createQueryBuilder()
            ->select('a.id, a.name, a.set, a.houses, a.sas, a.wins, a.losses, a.extra_data, a.owner')
            ->addSelect('string_agg(b.id::varchar, \',\') as tags')
            ->from(self::TABLE, 'a')
            ->leftJoin('a', self::TABLE_TAG_RELATION, 'b', 'a.id = b.deck_id')
            ->where('a.id in (:ids)')
            ->setParameter('ids', \array_map(static fn (Uuid $id) => $id->value(), $ids), Connection::PARAM_STR_ARRAY)
            ->groupBy('a.id')
            ->execute()
            ->fetchAllAssociative();

        if ([] === $result || false === $result) {
            return [];
        }

        return \array_map(fn (array $deck) => $this->map($deck), $result);
    }

    public function byNames(string ...$decks): array
    {
        $result = $this->connection->createQueryBuilder()
            ->select('a.id, a.name, a.set, a.houses, a.sas, a.wins, a.losses, a.extra_data, a.owner')
            ->from(self::TABLE, 'a')
            ->where('a.name in (:decks)')
            ->setParameter('decks', $decks, Connection::PARAM_STR_ARRAY)
            ->execute()
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
                    INSERT INTO %s (id, name, set, houses, sas, wins, losses, extra_data, owner)
                    VALUES (:id, :name, :set, :houses, :sas, :wins, :losses, :extra_data, :owner)
                    ON CONFLICT (id) DO UPDATE SET
                        id = :id,
                        name = :name,
                        set = :set,
                        houses = :houses,
                        sas = :sas,
                        wins = :wins,
                        losses = :losses,
                        extra_data = :extra_data,
                        owner = :owner
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

        $stmt->execute();
    }

    public function assignTags(Uuid $deckId, array $tags): void
    {
        $stmtDelete = $this->connection->prepare(
            \sprintf(
                'DELETE FROM %s WHERE deck_id = :deck_id',
                self::TABLE_TAG_RELATION,
            ),
        );

        $stmtDelete->bindValue(':deck_id', $deckId->value());

        $stmtDelete->execute();

        foreach ($tags as $tag) {
            $stmt = $this->connection->prepare(
                \sprintf(
                    'INSERT INTO %s (id, deck_id) VALUES (:id, :deck_id)',
                    self::TABLE_TAG_RELATION,
                ),
            );

            $stmt->bindValue(':id', $tag);
            $stmt->bindValue(':deck_id', $deckId->value());

            $stmt->execute();
        }
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
            \json_decode($deck['extra_data'], true, 512, \JSON_THROW_ON_ERROR),
            null === $deck['owner'] ? null : Uuid::from($deck['owner']),
            \array_key_exists('tags', $deck) && null !== $deck['tags']
                ? \explode(',', $deck['tags'])
                : [],
        );
    }
}
