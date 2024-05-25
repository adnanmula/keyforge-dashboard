<?php declare(strict_types=1);

namespace AdnanMula\Cards\Infrastructure\Persistence\Repository\Keyforge\Stat;

use AdnanMula\Cards\Application\Service\Json;
use AdnanMula\Cards\Domain\Model\Keyforge\Stat\KeyforgeStat;
use AdnanMula\Cards\Domain\Model\Keyforge\Stat\KeyforgeStatRepository;
use AdnanMula\Cards\Domain\Model\Keyforge\Stat\ValueObject\KeyforgeStatCategory;
use AdnanMula\Cards\Domain\Model\Shared\ValueObject\Uuid;
use AdnanMula\Cards\Infrastructure\Persistence\Repository\DbalRepository;

final class KeyforgeStatDbalRepository extends DbalRepository implements KeyforgeStatRepository
{
    private const TABLE = 'keyforge_stats';

    public function by(KeyforgeStatCategory $category, ?Uuid $reference): ?KeyforgeStat
    {
        $query = $this->connection->createQueryBuilder()
            ->select('a.*')
            ->from(self::TABLE, 'a')
            ->where('a.category = :category')
            ->setParameter('category', $category->value)
            ->setMaxResults(1);

        if (null === $reference) {
            $query->andWhere('a.reference is null');
        } else {
            $query->andWhere('a.reference = :reference')
                ->setParameter('reference', $reference->value());
        }

        $result = $query->executeQuery()->fetchAssociative();

        if (false === $result || [] === $result) {
            return null;
        }

        return $this->map($result);
    }

    public function save(KeyforgeStat $stat): void
    {
        $stmt = $this->connection->prepare(
            \sprintf(
                '
                INSERT INTO %s (id, category,reference, data)
                VALUES (:id, :category, :reference, :data)
                ON CONFLICT (id) DO UPDATE SET
                    id = :id,
                    category = :category,
                    reference = :reference,
                    data = :data
                ',
                self::TABLE,
            ),
        );

        $stmt->bindValue(':id', $stat->id->value());
        $stmt->bindValue(':category', $stat->category->value);
        $stmt->bindValue(':reference', $stat->reference?->value());
        $stmt->bindValue(':data', Json::encode($stat->data));

        $stmt->executeStatement();
    }

    public function remove(KeyforgeStatCategory $category, ?Uuid $reference): void
    {
        $query = $this->connection->createQueryBuilder()
            ->delete(self::TABLE, 'a')
            ->where('a.category = :category')
            ->setParameter('category', $category->value);

        if (null === $reference) {
            $query->andWhere('a.reference is null');
        } else {
            $query->andWhere('a.reference = :reference')
                ->setParameter('reference', $reference->value());
        }

        $query->executeStatement();
    }

    private function map(array $row): KeyforgeStat
    {
        return new KeyforgeStat(
            Uuid::from($row['id']),
            KeyforgeStatCategory::from($row['category']),
            null === $row['reference'] ? null : Uuid::from($row['reference']),
            Json::decode($row['data']),
        );
    }
}
