<?php declare(strict_types=1);

namespace AdnanMula\Cards\Infrastructure\Persistence\Repository\Keyforge\User;

use AdnanMula\Cards\Domain\Model\Keyforge\User\KeyforgeUser;
use AdnanMula\Cards\Domain\Model\Keyforge\User\KeyforgeUserRepository;
use AdnanMula\Cards\Domain\Model\Shared\ValueObject\Uuid;
use AdnanMula\Cards\Infrastructure\Persistence\Repository\DbalRepository;
use AdnanMula\Criteria\Criteria;
use AdnanMula\Criteria\DbalCriteriaAdapter;

final class KeyforgeUserDbalRepository extends DbalRepository implements KeyforgeUserRepository
{
    private const TABLE = 'keyforge_users';

    private const FIELD_MAPPING = [
        'id' => 'a.id',
        'is_external' => 'a.owner',
    ];

    public function search(Criteria $criteria): array
    {
        $builder = $this->connection->createQueryBuilder();

        $query = $builder->select('a.*')->from(self::TABLE, 'a');

        (new DbalCriteriaAdapter($builder, self::FIELD_MAPPING))->execute($criteria);

        $result = $query->executeQuery()->fetchAllAssociative();

        return \array_map(fn (array $row) => $this->map($row), $result);
    }

    public function save(KeyforgeUser $user): void
    {
        $stmt = $this->connection->prepare(
            \sprintf(
                'INSERT INTO %s (id, name, owner)
                VALUES (:id, :name, :owner)
                ON CONFLICT (id) DO NOTHING',
                self::TABLE,
            ),
        );

        $stmt->bindValue(':id', $user->id()->value());
        $stmt->bindValue(':name', $user->name());
        $stmt->bindValue(':owner', $user->owner()?->value());

        $stmt->executeStatement();
    }

    private function map(array $user): KeyforgeUser
    {
        return KeyforgeUser::create(
            Uuid::from($user['id']),
            $user['name'],
            Uuid::fromNullable($user['owner']),
        );
    }
}
