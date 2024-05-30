<?php declare(strict_types=1);

namespace AdnanMula\Cards\Infrastructure\Persistence\Repository\Keyforge\User;

use AdnanMula\Cards\Domain\Model\Keyforge\User\KeyforgeUser;
use AdnanMula\Cards\Domain\Model\Keyforge\User\KeyforgeUserRepository;
use AdnanMula\Cards\Domain\Model\Shared\ValueObject\Uuid;
use AdnanMula\Cards\Infrastructure\Persistence\Repository\DbalRepository;
use AdnanMula\Criteria\Criteria;
use AdnanMula\Criteria\DbalCriteriaAdapter;
use Doctrine\DBAL\ArrayParameterType;

final class KeyforgeUserDbalRepository extends DbalRepository implements KeyforgeUserRepository
{
    private const TABLE = 'keyforge_users';
    private const TABLE_USERS = 'users';

    private const FIELD_MAPPING = [
        'is_external' => 'b.id'
    ];

    public function search(Criteria $criteria): array
    {
        $builder = $this->connection->createQueryBuilder();

        $query = $builder->select('a.*, b.id as id2')
            ->from(self::TABLE, 'a')
            ->leftJoin('a', self::TABLE_USERS, 'b', 'a.id = b.id');

        (new DbalCriteriaAdapter($builder, self::FIELD_MAPPING))->execute($criteria);

        $result = $query->executeQuery()->fetchAllAssociative();

        return \array_map(fn (array $row) => $this->map($row), $result);
    }

    public function byId(Uuid $id): ?KeyforgeUser
    {
        $result = $this->connection->createQueryBuilder()
            ->select('a.id, a.name')
            ->from(self::TABLE, 'a')
            ->where('a.id = :id')
            ->setParameter('id', $id->value())
            ->setMaxResults(1)
            ->executeQuery()
            ->fetchAssociative();

        if (false === $result) {
            return null;
        }

        return $this->map($result);
    }

    public function byName(string $name): ?KeyforgeUser
    {
        $result = $this->connection->createQueryBuilder()
            ->select('a.id, a.name')
            ->from(self::TABLE, 'a')
            ->where('a.name = :name')
            ->setParameter('name', $name)
            ->setMaxResults(1)
            ->executeQuery()
            ->fetchAssociative();

        if (false === $result) {
            return null;
        }

        return $this->map($result);
    }

    public function byIds(Uuid ...$ids): array
    {
        $result = $this->connection->createQueryBuilder()
            ->select('a.id, a.name')
            ->from(self::TABLE, 'a')
            ->where('a.id in (:ids)')
            ->setParameter('ids', \array_map(static fn (Uuid $id) => $id->value(), $ids), ArrayParameterType::STRING)
            ->executeQuery()
            ->fetchAllAssociative();

        if ([] === $result || false === $result) {
            return [];
        }

        return \array_map(fn (array $user) => $this->map($user), $result);
    }

    public function save(KeyforgeUser $user): void
    {
        $stmt = $this->connection->prepare(
            \sprintf(
                'INSERT INTO %s (id, name)
                VALUES (:id, :name)
                ON CONFLICT (id) DO UPDATE SET
                id = :id,
                name = :name',
                self::TABLE,
            ),
        );

        $stmt->bindValue(':id', $user->id()->value());
        $stmt->bindValue(':name', $user->name());

        $stmt->executeStatement();
    }

    private function map(array $user): KeyforgeUser
    {
        return KeyforgeUser::create(
            Uuid::from($user['id']),
            $user['name'],
            null === ($user['id2'] ?? null),
        );
    }
}
