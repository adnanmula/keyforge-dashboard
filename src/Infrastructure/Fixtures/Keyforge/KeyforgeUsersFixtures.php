<?php declare(strict_types=1);

namespace AdnanMula\Cards\Infrastructure\Fixtures\Keyforge;

use AdnanMula\Cards\Domain\Model\Keyforge\KeyforgeUser;
use AdnanMula\Cards\Domain\Model\Shared\ValueObject\Uuid;
use AdnanMula\Cards\Domain\Service\Persistence\Fixture;
use AdnanMula\Cards\Infrastructure\Fixtures\DbalFixture;
use AdnanMula\Cards\Infrastructure\Fixtures\User\UserFixtures;
use Doctrine\DBAL\ParameterType;

final class KeyforgeUsersFixtures extends DbalFixture implements Fixture
{
    public const FIXTURE_KF_USER_1_ID = UserFixtures::FIXTURE_USER_1_ID;
    public const FIXTURE_KF_USER_2_ID = UserFixtures::FIXTURE_USER_2_ID;
    public const FIXTURE_KF_USER_3_ID = 'b889fac0-6ddb-41fe-95c2-3df1230111c6';

    private const TABLE = 'keyforge_users';

    private bool $loaded = false;

    public function load(): void
    {
        $this->save(KeyforgeUser::create(Uuid::from(self::FIXTURE_KF_USER_1_ID), 'username', false));
        $this->save(KeyforgeUser::create(Uuid::from(self::FIXTURE_KF_USER_2_ID), 'username2', false));
        $this->save(KeyforgeUser::create(Uuid::from(self::FIXTURE_KF_USER_3_ID), 'user-without-login', true));

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

    private function save(KeyforgeUser $user): void
    {
        $stmt = $this->connection->prepare(
            \sprintf(
                '
                INSERT INTO %s (id, name, external)
                VALUES (:id, :name, :external)
                ON CONFLICT (id) DO UPDATE SET
                    id = :id,
                    name = :name,
                    external = :external
                ',
                self::TABLE,
            ),
        );

        $stmt->bindValue(':id', $user->id()->value());
        $stmt->bindValue(':name', $user->name());
        $stmt->bindValue(':external', $user->external(), ParameterType::BOOLEAN);

        $stmt->execute();
    }
}
