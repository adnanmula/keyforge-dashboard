<?php declare(strict_types=1);

namespace AdnanMula\Cards\Infrastructure\Fixtures\User;

use AdnanMula\Cards\Domain\Model\Shared\User;
use AdnanMula\Cards\Domain\Model\Shared\ValueObject\Uuid;
use AdnanMula\Cards\Domain\Service\Persistence\Fixture;
use AdnanMula\Cards\Infrastructure\Fixtures\DbalFixture;

final class UserFixtures extends DbalFixture implements Fixture
{
    private const TABLE = 'users';

    private bool $loaded = false;

    public function load(): void
    {
        $this->save(new User(
            Uuid::v4(),
            'a',
            '$2y$13$sn3JyhOwj1wsvfQQ.0TwZeXVISv8fpFo8v09sa9cSfm0C2Psh49mO',
            ['ROLE_BASIC', 'ROLE_KEYFORGE'],
        ));

        $this->save(new User(
            Uuid::v4(),
            'b',
            '$2y$13$K8M5NdlCkAQUrOCh0cr.CuM.nX4DVeGxbeZBrUi.FTAY/gW/7NpJm',
            ['ROLE_BASIC'],
        ));

        $this->loaded = true;
    }

    public function isLoaded(): bool
    {
        return $this->loaded;
    }

    public function dependants(): array
    {
        return [];
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
}
