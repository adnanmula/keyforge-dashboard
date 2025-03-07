<?php declare(strict_types=1);

namespace AdnanMula\Cards\Infrastructure\Persistence\Repository\Keyforge\Deck;

use AdnanMula\Cards\Domain\Model\Shared\ValueObject\Uuid;
use AdnanMula\Cards\Infrastructure\Persistence\Repository\DbalRepository;

final class KeyforgeDeckUpdateDbalRepository extends DbalRepository
{
    private const string TABLE = 'keyforge_decks_stats_update';

    public function add(Uuid $id): void
    {
        $stmt = $this->connection->prepare(
            \sprintf(
                'INSERT INTO %s (id, at) VALUES (:id, :at) ON CONFLICT (id) DO NOTHING',
                self::TABLE,
            ),
        );

        $stmt->bindValue(':id', $id->value());
        $stmt->bindValue(':at', (new \DateTimeImmutable())->format(\DateTimeInterface::ATOM));

        $stmt->executeStatement();
    }

    public function isUpdated(Uuid $id): bool
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
            return false;
        }

        return true;
    }

    public function all(): array
    {
        return $this->connection->createQueryBuilder()
            ->select('a.id')
            ->from(self::TABLE, 'a')
            ->executeQuery()
            ->fetchFirstColumn();
    }
}
