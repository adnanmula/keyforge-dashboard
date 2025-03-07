<?php declare(strict_types=1);

namespace AdnanMula\Cards\Infrastructure\Fixtures\User;

use AdnanMula\Cards\Application\Service\Json;
use AdnanMula\Cards\Domain\Model\Shared\User;
use AdnanMula\Cards\Domain\Model\Shared\ValueObject\Locale;
use AdnanMula\Cards\Domain\Model\Shared\ValueObject\Uuid;
use AdnanMula\Cards\Domain\Service\Persistence\Fixture;
use AdnanMula\Cards\Infrastructure\Fixtures\DbalFixture;

final class UserFixtures extends DbalFixture implements Fixture
{
    public const string FIXTURE_USER_1_ID = '426117e9-e016-4f53-be1f-4eb8711ce625';
    public const string FIXTURE_USER_2_ID = '97a7e9fe-ff27-4d52-83c0-df4bc9309fb0';
    public const string FIXTURE_USER_3_ID = '048528d9-8545-48cd-b6c9-c50ec34c889e';

    private const string TABLE = 'users';

    private bool $loaded = false;

    public function load(): void
    {
        $this->save(new User(
            Uuid::from(self::FIXTURE_USER_1_ID),
            'a',
            '$2y$13$sn3JyhOwj1wsvfQQ.0TwZeXVISv8fpFo8v09sa9cSfm0C2Psh49mO',
            Locale::es_ES,
            ['ROLE_ADMIN'],
        ));

        $this->save(new User(
            Uuid::from(self::FIXTURE_USER_2_ID),
            'b',
            '$2y$13$K8M5NdlCkAQUrOCh0cr.CuM.nX4DVeGxbeZBrUi.FTAY/gW/7NpJm',
            Locale::en_GB,
            ['ROLE_KEYFORGE'],
        ));

        $this->save(new User(
            Uuid::from(self::FIXTURE_USER_3_ID),
            'c',
            '$2y$13$rWCZ1rvDCKLrjfTOGWkDMeL9uiAWt7T6ly6mIID38.fx780eNHV9S',
            Locale::en_GB,
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
                    INSERT INTO %s (id, name, password, locale, roles)
                    VALUES (:id, :name, :password, :locale, :roles)
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
}
