<?php declare(strict_types=1);

namespace AdnanMula\Cards\Infrastructure\Persistence\Repository\Shared;

use AdnanMula\Cards\Domain\Model\Shared\User;
use AdnanMula\Cards\Domain\Model\Shared\UserRepository;
use AdnanMula\Cards\Domain\Model\Shared\ValueObject\Uuid;
use AdnanMula\Cards\Infrastructure\Persistence\Repository\DbalRepository;

final class UserDbalRepository extends DbalRepository implements UserRepository
{
    private const TABLE = 'users';

    public function byName(string $name): ?User
    {
        $result = $this->connection
            ->createQueryBuilder()
            ->select('a.id, a.name, a.password, a.roles')
            ->from(self::TABLE, 'a')
            ->where('a.name = :name')
            ->setParameter('name', $name)
            ->setMaxResults(1)
            ->execute()
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
                    INSERT INTO %s (id, name, password, roles)
                    VALUES (:id, :name, :password, :roles)
                ',
                self::TABLE,
            ),
        );

        $stmt->bindValue(':id', $user->id()->value());
        $stmt->bindValue(':name', $user->name());
        $stmt->bindValue(':password', $user->getPassword());
        $stmt->bindValue(':roles', \json_encode($user->getRoles()));

        $stmt->execute();
    }

    private function map(array $result): User
    {
        return new User(
            Uuid::from($result['id']),
            $result['name'],
            $result['password'],
            \json_decode($result['roles'], true, 512, \JSON_THROW_ON_ERROR),
        );
    }
}
