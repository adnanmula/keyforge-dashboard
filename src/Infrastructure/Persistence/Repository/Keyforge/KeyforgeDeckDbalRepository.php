<?php declare(strict_types=1);

namespace AdnanMula\Cards\Infrastructure\Persistence\Repository\Keyforge;

use AdnanMula\Cards\Application\Service\Json;
use AdnanMula\Cards\Domain\Model\Keyforge\KeyforgeDeck;
use AdnanMula\Cards\Domain\Model\Keyforge\KeyforgeDeckRepository;
use AdnanMula\Cards\Domain\Model\Keyforge\ValueObject\KeyforgeDeckHouses;
use AdnanMula\Cards\Domain\Model\Keyforge\ValueObject\KeyforgeHouse;
use AdnanMula\Cards\Domain\Model\Keyforge\ValueObject\KeyforgeSet;
use AdnanMula\Cards\Domain\Model\Shared\QueryOrder;
use AdnanMula\Cards\Domain\Model\Shared\ValueObject\Uuid;
use AdnanMula\Cards\Infrastructure\Persistence\Repository\DbalRepository;
use Doctrine\DBAL\Connection;

final class KeyforgeDeckDbalRepository extends DbalRepository implements KeyforgeDeckRepository
{
    private const TABLE = 'keyforge_decks';

    public function all(
        int $start,
        int $length,
        ?string $deckName = null,
        ?string $set = null,
        ?string $house = null,
        ?Uuid $owner = null,
        ?QueryOrder $order = null
    ): array {
        $builder = $this->connection->createQueryBuilder();

        $query = $builder->select('a.id, a.name, a.set, a.houses, a.sas, a.wins, a.losses, a.extra_data, a.owner')
            ->from(self::TABLE, 'a')
            ->setFirstResult($start)
            ->setMaxResults($length);

//      TODO quitar esto de aqui
        if (null !== $order) {
            if ('win_rate' === $order->field()) {
                if (\strtolower($order->order()) === 'desc') {
                    $query->orderBy('a.wins', 'DESC')
                        ->addOrderBy('a.losses', 'ASC');
                } else {
                    $query->orderBy('a.wins', 'ASC')
                        ->addOrderBy('a.losses', 'DESC');
                }
            } else {
                $query->orderBy('a.' . $order->field(), $order->order());
            }
        } else {
            $query->orderBy('a.wins', 'DESC')
                ->addOrderBy('a.losses', 'ASC');
        }

        if (null !== $owner) {
            $query->andWhere('a.owner = :owner')
                ->setParameter('owner', $owner->value());
        }

        if (null !== $deckName) {
            $query->andWhere('a.name ilike :deck_name')
                ->setParameter('deck_name', '%' . $deckName . '%');
        }

        if (null !== $set) {
            $query->andWhere('a.set = :set')
                ->setParameter('set', $set);
        }

        if (null !== $house) {
            $query->andWhere($builder->expr()->or(
                'a.houses->>0 = :house',
                'a.houses->>1 = :house',
                'a.houses->>2 = :house',
            ))->setParameter('house', $house);
        }

        $result = $query->execute()->fetchAllAssociative();

        return \array_map(fn (array $row) => $this->map($row), $result);
    }

    public function count(?string $deckName = null, ?string $set = null, ?string $house = null, ?Uuid $owner = null): int
    {
        $builder = $this->connection->createQueryBuilder();
        $query = $builder->select('COUNT(a.id)')
            ->from(self::TABLE, 'a');

        if (null !== $owner) {
            $query->andWhere('a.owner = :owner')
                ->setParameter('owner', $owner->value());
        }

        if (null !== $deckName) {
            $query->andWhere('a.name ilike :deck_name')
                ->setParameter('deck_name', '%' . $deckName . '%');
        }

        if (null !== $set) {
            $query->andWhere('a.set = :set')
                ->setParameter('set', $set);
        }

        if (null !== $house) {
            $query->andWhere($builder->expr()->or(
                'a.houses->>0 = :house',
                'a.houses->>1 = :house',
                'a.houses->>2 = :house',
            ))->setParameter('house', $house);
        }

        return $query->execute()->fetchOne();
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

    private function map(array $deck): KeyforgeDeck
    {
        return new KeyforgeDeck(
            Uuid::from($deck['id']),
            $deck['name'],
            KeyforgeSet::from($deck['set']),
            KeyforgeDeckHouses::from(
                ...\array_map(
                    static fn (string $house) => KeyforgeHouse::from($house),
                    \json_decode($deck['houses'], true, 512, \JSON_THROW_ON_ERROR),
                ),
            ),
            $deck['sas'],
            $deck['wins'],
            $deck['losses'],
            \json_decode($deck['extra_data'], true, 512, \JSON_THROW_ON_ERROR),
            null === $deck['owner'] ? null : Uuid::from($deck['owner']),
        );
    }
}
