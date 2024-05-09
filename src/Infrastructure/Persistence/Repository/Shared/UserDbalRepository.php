<?php declare(strict_types=1);

namespace AdnanMula\Cards\Infrastructure\Persistence\Repository\Shared;

use AdnanMula\Cards\Application\Service\Json;
use AdnanMula\Cards\Domain\Model\Shared\User;
use AdnanMula\Cards\Domain\Model\Shared\UserRepository;
use AdnanMula\Cards\Domain\Model\Shared\ValueObject\Locale;
use AdnanMula\Cards\Domain\Model\Shared\ValueObject\UserRole;
use AdnanMula\Cards\Domain\Model\Shared\ValueObject\Uuid;
use AdnanMula\Cards\Infrastructure\Persistence\Repository\DbalRepository;
use Doctrine\DBAL\ParameterType;

final class UserDbalRepository extends DbalRepository implements UserRepository
{
    private const TABLE = 'users';
    private const TABLE_FRIENDS = 'user_friends';

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

    public function byRole(UserRole $role): array
    {
        $result = $this->connection->createQueryBuilder()
            ->select('a.id, a.name, a.password, a.locale, a.roles')
            ->from(self::TABLE, 'a')
            ->where('a.roles::jsonb @> \'["' . $role->value . '"]\'::jsonb')
            ->executeQuery()
            ->fetchAllAssociative();

        return \array_map(fn (array $row) => $this->map($row, false), $result);
    }

    public function friends(Uuid $id): array
    {
        return $this->connection->createQueryBuilder()
            ->select('a.*, b.name as receiver_name, c.name as sender_name')
            ->from(self::TABLE_FRIENDS, 'a')
            ->innerJoin('a', self::TABLE, 'b', 'a.friend_id = b.id')
            ->innerJoin('b', self::TABLE, 'c', 'a.id = c.id')
            ->where('a.id = :id')
            ->orWhere('a.friend_id = :id')
            ->setParameter('id', $id->value())
            ->executeQuery()
            ->fetchAllAssociative();
    }

    public function friendRequest(Uuid $user, Uuid $friend): ?array
    {
        $result = $this->connection->createQueryBuilder()
            ->select('a.*, b.name')
            ->from(self::TABLE_FRIENDS, 'a')
            ->innerJoin('a', self::TABLE, 'b', 'a.friend_id = b.id')
            ->where('a.id = :user')
            ->andWhere('a.friend_id = :friend_id')
            ->andWhere('a.is_request is true')
            ->setParameter('user', $user->value())
            ->setParameter('friend_id', $friend->value())
            ->executeQuery()
            ->fetchAssociative();

        if (false === $result) {
            return null;
        }

        return $result;
    }

    public function addFriend(Uuid $id, Uuid $friend, bool $isRequest): void
    {
        $stmt = $this->connection->prepare(
            \sprintf(
                '
                    INSERT INTO %s (id, friend_id, is_request)
                    VALUES (:id, :friend_id, :is_request)
                    ON CONFLICT (id, friend_id) DO UPDATE SET is_request = :is_request
                ',
                self::TABLE_FRIENDS,
            ),
        );

        $stmt->bindValue(':id', $id->value());
        $stmt->bindValue(':friend_id', $friend->value());
        $stmt->bindValue(':is_request', $isRequest, ParameterType::BOOLEAN);

        $stmt->executeStatement();
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
