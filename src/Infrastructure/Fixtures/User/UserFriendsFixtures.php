<?php declare(strict_types=1);

namespace AdnanMula\Cards\Infrastructure\Fixtures\User;

use AdnanMula\Cards\Domain\Model\Shared\ValueObject\Uuid;
use AdnanMula\Cards\Domain\Service\Persistence\Fixture;
use AdnanMula\Cards\Infrastructure\Fixtures\DbalFixture;
use Doctrine\DBAL\ParameterType;

final class UserFriendsFixtures extends DbalFixture implements Fixture
{
    private const string TABLE = 'user_friends';

    private bool $loaded = false;

    public function load(): void
    {
        $this->save(
            Uuid::from(UserFixtures::FIXTURE_USER_1_ID),
            Uuid::from(UserFixtures::FIXTURE_USER_2_ID),
            false,
        );

        $this->save(
            Uuid::from(UserFixtures::FIXTURE_USER_2_ID),
            Uuid::from(UserFixtures::FIXTURE_USER_1_ID),
            true,
        );

        $this->loaded = true;
    }

    public function isLoaded(): bool
    {
        return $this->loaded;
    }

    public function dependants(): array
    {
        return [UserFixtures::class];
    }

    public function save(Uuid $user, Uuid $friend, bool $isRequest): void
    {
        $stmt = $this->connection->prepare(
            \sprintf(
                '
                    INSERT INTO %s (id, friend_id, is_request)
                    VALUES (:id, :friend_id, :is_request)
                ',
                self::TABLE,
            ),
        );

        $stmt->bindValue(':id', $user->value());
        $stmt->bindValue(':friend_id', $friend->value());
        $stmt->bindValue(':is_request', $isRequest, ParameterType::BOOLEAN);

        $stmt->executeStatement();
    }
}
