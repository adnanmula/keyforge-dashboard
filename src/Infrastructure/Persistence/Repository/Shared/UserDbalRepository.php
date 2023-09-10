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

        return $this->map($result);
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

        return $this->map($result);
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

        $stmt->execute();
    }

    private function map(array $result): User
    {
        return new User(
            Uuid::from($result['id']),
            $result['name'],
            $result['password'],
            Locale::from($result['locale']),
            Json::decode($result['roles']),
        );
    }
}
