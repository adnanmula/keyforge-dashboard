<?php declare(strict_types=1);

namespace AdnanMula\Cards\Infrastructure\Persistence\Repository\User;

use AdnanMula\Cards\Domain\Model\Shared\ValueObject\Uuid;
use AdnanMula\Cards\Domain\Model\User\User;
use AdnanMula\Cards\Domain\Model\User\UserRepository;
use AdnanMula\Cards\Infrastructure\Persistence\Repository\DbalRepository;
use Doctrine\DBAL\Connection;

final class UserDbalRepository extends DbalRepository implements UserRepository
{
    private const TABLE_USER = 'users';

    public function all(): array
    {
        $result = $this->connection
            ->createQueryBuilder()
            ->select('a.id, a.name')
            ->from(self::TABLE_USER, 'a')
            ->execute()
            ->fetchAllAssociative();

        return \array_map(fn (array $row) => $this->map($row), $result);
    }

    public function byId(Uuid $id): ?User
    {
        $result = $this->connection
            ->createQueryBuilder()
            ->select('a.id, a.name')
            ->from(self::TABLE_USER, 'a')
            ->where('a.id = :id')
            ->setParameter('id', $id->value())
            ->setMaxResults(1)
            ->execute()
            ->fetchAssociative();

        if (false === $result) {
            return null;
        }

        return $this->map($result);
    }

    public function byIds(Uuid ...$ids): array
    {
        $result = $this->connection
            ->createQueryBuilder()
            ->select('a.id, a.name')
            ->from(self::TABLE_USER, 'a')
            ->where('a.id in (:ids)')
            ->setParameter('ids', \array_map(static fn (Uuid $id) => $id->value(), $ids),Connection::PARAM_STR_ARRAY)
            ->execute()
            ->fetchAllAssociative();

        if (false === $result) {
            return [];
        }

        return \array_map(fn (array $user) => $this->map($user), $result);
    }

    public function save(User $user): void
    {
        $stmt = $this->connection->prepare(
            \sprintf(
                'INSERT INTO %s (id, name)
                VALUES (:id, :name)
                ON CONFLICT (id) DO UPDATE SET
                id = :id,
                name = :name',
                self::TABLE_USER,
            ),
        );

        $stmt->bindValue(':id', $user->id()->value());
        $stmt->bindValue(':name', $user->name());

        $stmt->execute();
    }

    private function map($user): User
    {
        return User::create(
            Uuid::from($user['id']),
            $user['name'],
        );
    }
}
