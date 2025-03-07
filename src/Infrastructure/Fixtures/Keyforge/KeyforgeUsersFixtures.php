<?php declare(strict_types=1);

namespace AdnanMula\Cards\Infrastructure\Fixtures\Keyforge;

use AdnanMula\Cards\Domain\Model\Keyforge\User\KeyforgeUser;
use AdnanMula\Cards\Domain\Model\Shared\ValueObject\Uuid;
use AdnanMula\Cards\Domain\Service\Persistence\Fixture;
use AdnanMula\Cards\Infrastructure\Fixtures\DbalFixture;
use AdnanMula\Cards\Infrastructure\Fixtures\User\UserFixtures;

final class KeyforgeUsersFixtures extends DbalFixture implements Fixture
{
    public const string FIXTURE_KF_USER_1_ID = UserFixtures::FIXTURE_USER_1_ID;
    public const string FIXTURE_KF_USER_2_ID = UserFixtures::FIXTURE_USER_2_ID;
    public const string FIXTURE_KF_USER_3_ID = 'b889fac0-6ddb-41fe-95c2-3df1230111c6';
    public const string FIXTURE_KF_USER_4_ID = '8031c24d-6ec1-4a4c-abc9-fc7d7dc72693';

    private const string TABLE = 'keyforge_users';

    private bool $loaded = false;

    public function load(): void
    {
        $this->save(KeyforgeUser::create(Uuid::from(self::FIXTURE_KF_USER_1_ID), 'username', null));
        $this->save(KeyforgeUser::create(Uuid::from(self::FIXTURE_KF_USER_2_ID), 'username2', null));
        $this->save(KeyforgeUser::create(Uuid::from(self::FIXTURE_KF_USER_3_ID), 'user-without-login', null));
        $this->save(KeyforgeUser::create(Uuid::from(self::FIXTURE_KF_USER_4_ID), 'user4', null));

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
                INSERT INTO %s (id, name)
                VALUES (:id, :name)
                ON CONFLICT (id) DO UPDATE SET
                    id = :id,
                    name = :name
                ',
                self::TABLE,
            ),
        );

        $stmt->bindValue(':id', $user->id()->value());
        $stmt->bindValue(':name', $user->name());

        $stmt->executeStatement();
    }
}
