<?php declare(strict_types=1);

namespace AdnanMula\Cards\Infrastructure\Persistence\Repository\Shared;

use AdnanMula\Cards\Application\Service\Json;
use AdnanMula\Cards\Domain\Model\Shared\User;
use AdnanMula\Cards\Domain\Model\Shared\UserRepository;
use AdnanMula\Cards\Domain\Model\Shared\ValueObject\Locale;
use AdnanMula\Cards\Domain\Model\Shared\ValueObject\UserRole;
use AdnanMula\Cards\Domain\Model\Shared\ValueObject\Uuid;
use AdnanMula\Cards\Infrastructure\Persistence\Repository\DbalRepository;
use Doctrine\DBAL\ArrayParameterType;
use Doctrine\DBAL\ParameterType;

final class UserDbalRepository extends DbalRepository implements UserRepository
{
    private const string TABLE = 'users';
    private const string TABLE_FRIENDS = 'user_friends';

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

    public function byIds(Uuid ...$ids): array
    {
        $result = $this->connection->createQueryBuilder()
            ->select('a.id, a.name, a.password, a.locale, a.roles')
            ->from(self::TABLE, 'a')
            ->where('a.id in (:ids)')
            ->setParameter('ids', \array_map(static fn (Uuid $id): string => $id->value(), $ids), ArrayParameterType::STRING)
            ->setMaxResults(1)
            ->executeQuery()
            ->fetchAllAssociative();

        return \array_map(fn (array $r) => $this->map($r, false), $result);
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

    public function byRoles(UserRole ...$roles): array
    {
        $query = $this->connection->createQueryBuilder()
            ->select('a.id, a.name, a.password, a.locale, a.roles')
            ->from(self::TABLE, 'a');

        foreach ($roles as $role) {
            $query->orWhere('a.roles::jsonb @> \'["' . $role->value . '"]\'::jsonb');

        }

        $result = $query->executeQuery()->fetchAllAssociative();

        return \array_map(fn (array $row) => $this->map($row, false), $result);
    }

    public function friends(Uuid $id, ?bool $isRequest = null): array
    {
        $qb = $this->connection->createQueryBuilder();

        $query = $qb->select('a.*, b.name as receiver_name, c.name as sender_name')
            ->from(self::TABLE_FRIENDS, 'a')
            ->innerJoin('a', self::TABLE, 'b', 'a.friend_id = b.id')
            ->innerJoin('b', self::TABLE, 'c', 'a.id = c.id')
            ->where($qb->expr()->or('a.id = :id', 'a.friend_id = :id'))
            ->setParameter('id', $id->value());

        if (null !== $isRequest) {
            $query->andWhere('a.is_request = :is_request')
                ->setParameter('is_request', $isRequest, ParameterType::BOOLEAN);
        }

        return $query->executeQuery()->fetchAllAssociative();
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
