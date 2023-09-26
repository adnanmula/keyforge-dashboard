<?php declare(strict_types=1);

namespace AdnanMula\Cards\Infrastructure\Persistence\Repository\Shared;

use AdnanMula\Cards\Application\Service\Json;
use AdnanMula\Cards\Domain\Model\Shared\User;
use AdnanMula\Cards\Domain\Model\Shared\UserRepository;
use AdnanMula\Cards\Domain\Model\Shared\ValueObject\Locale;
use AdnanMula\Cards\Domain\Model\Shared\ValueObject\Uuid;
use AdnanMula\Cards\Infrastructure\Persistence\Repository\DbalRepository;

final class UserDbalRepository extends DbalRepository implements UserRepository
{
    private const TABLE = 'users';
    private const TABLE_FRIENDS = 'user_friends';

    public function byId(Uuid $id): ?User
    {
        $result = $this->connection->createQueryBuilder()
            ->select('a.id, a.name, a.password, a.locale, a.roles')
            ->from(self::TABLE, 'a')
            ->where('a.id = :id')
            ->setParameter('id', $id->value())
            ->setMaxResults(1)
            ->executeQuery()
            ->fetchAssociative();

        if (false === $result) {
            return null;
        }

        return $this->map($result, true);
    }

    public function byName(string $name): ?User
    {
        $result = $this->connection->createQueryBuilder()
            ->select('a.id, a.name, a.password, a.locale, a.roles')
            ->from(self::TABLE, 'a')
            ->where('a.name = :name')
            ->setParameter('name', $name)
            ->setMaxResults(1)
            ->executeQuery()
            ->fetchAssociative();

        if (false === $result) {
            return null;
        }

        return $this->map($result, true);
    }

    public function save(User $user): void
    {
        $stmt = $this->connection->prepare(
            \sprintf(
                '
                    INSERT INTO %s (id, name, password, locale, roles)
                    VALUES (:id, :name, :password, :locale, :roles)
                    ON CONFLICT (id) DO UPDATE SET
                        name = :name,
                        password = :password,
                        locale = :locale,
                        roles = :roles
                ',
                self::TABLE,
            ),
        );

        $stmt->bindValue(':id', $user->id()->value());
        $stmt->bindValue(':name', $user->name());
        $stmt->bindValue(':password', $user->getPassword());
        $stmt->bindValue(':locale', $user->locale()->value);
        $stmt->bindValue(':roles', Json::encode($user->getRoles()));

        $stmt->executeStatement();
    }

    public function friends(Uuid $id): array
    {
        $result = $this->connection->createQueryBuilder()
            ->select('a.*, b.name')
            ->from(self::TABLE_FRIENDS, 'a')
            ->innerJoin('a', self::TABLE, 'b', 'a.friend_id = b.id')
            ->where('a.id = :id')
            ->setParameter('id', $id->value())
            ->executeQuery()
            ->fetchAllAssociative();

        return $result;
    }

    public function removeFriend(Uuid $id, Uuid $friendId): void
    {
        $stmt = $this->connection->prepare(
            \sprintf(
                'DELETE FROM %s a WHERE a.id = :id and a.friend_id = :friend_id',
                self::TABLE_FRIENDS,
            ),
        );

        $stmt->bindValue(':id', $id->value());
        $stmt->bindValue(':friend_id', $friendId->value());
        $stmt->executeStatement();
    }

    private function map(array $result, bool $mapPassword): User
    {
        return new User(
            Uuid::from($result['id']),
            $result['name'],
            $mapPassword ? $result['password'] : '',
            Locale::from($result['locale']),
            Json::decode($result['roles']),
        );
    }
}
