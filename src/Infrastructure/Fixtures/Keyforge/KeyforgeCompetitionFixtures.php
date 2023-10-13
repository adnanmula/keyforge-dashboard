<?php declare(strict_types=1);

namespace AdnanMula\Cards\Infrastructure\Fixtures\Keyforge;

use AdnanMula\Cards\Application\Service\Json;
use AdnanMula\Cards\Domain\Model\Keyforge\KeyforgeCompetition;
use AdnanMula\Cards\Domain\Model\Shared\ValueObject\CompetitionType;
use AdnanMula\Cards\Domain\Model\Shared\ValueObject\Uuid;
use AdnanMula\Cards\Domain\Service\Persistence\Fixture;
use AdnanMula\Cards\Infrastructure\Fixtures\DbalFixture;

final class KeyforgeCompetitionFixtures extends DbalFixture implements Fixture
{
    public const FIXTURE_KEYFORGE_COMPETITION_1_ID = 'dbeec29a-d227-45b4-9c00-709c550817a7';
    public const FIXTURE_KEYFORGE_COMPETITION_2_ID = 'c7dfd4c4-9351-4f0f-ba2f-ca5d6b0f57e1';

    private const TABLE = 'keyforge_competitions';

    private bool $loaded = false;

    public function load(): void
    {
        $this->save(
            new KeyforgeCompetition(
                Uuid::from(self::FIXTURE_KEYFORGE_COMPETITION_1_ID),
                'torneo-uno',
                'Torneo1',
                CompetitionType::ROUND_ROBIN_1,
                [
                    Uuid::from(KeyforgeUsersFixtures::FIXTURE_KF_USER_1_ID),
                    Uuid::from(KeyforgeUsersFixtures::FIXTURE_KF_USER_2_ID),
                    Uuid::from(KeyforgeUsersFixtures::FIXTURE_KF_USER_3_ID),
                ],
                'Description',
                new \DateTimeImmutable('2022-12-03'),
                null,
                null,
                null,
            ),
        );

        $this->save(
            new KeyforgeCompetition(
                Uuid::from(self::FIXTURE_KEYFORGE_COMPETITION_2_ID),
                'torneo-dos',
                'Torneo2',
                CompetitionType::ROUND_ROBIN_1,
                [
                    Uuid::from(KeyforgeUsersFixtures::FIXTURE_KF_USER_1_ID),
                    Uuid::from(KeyforgeUsersFixtures::FIXTURE_KF_USER_2_ID),
                    Uuid::from(KeyforgeUsersFixtures::FIXTURE_KF_USER_3_ID),
                ],
                'Description 2',
                new \DateTimeImmutable('2022-12-03'),
                new \DateTimeImmutable('2022-12-14'),
                new \DateTimeImmutable('2022-12-23'),
                Uuid::from(KeyforgeUsersFixtures::FIXTURE_KF_USER_1_ID),
            ),
        );

        $this->loaded = true;
    }

    public function isLoaded(): bool
    {
        return $this->loaded;
    }

    public function dependants(): array
    {
        return [
            KeyforgeUsersFixtures::class,
            KeyforgeTagsFixtures::class,
            KeyforgeDecksFixtures::class,
            KeyforgeGamesFixtures::class,
        ];
    }

    private function save(KeyforgeCompetition $competition): void
    {
        $stmt = $this->connection->prepare(
            \sprintf(
                '
                    INSERT INTO %s (id, reference, name, competition_type, users, description, created_at, started_at, finished_at, winner)
                    VALUES (:id, :reference, :name, :competition_type, :users, :description, :created_at, :started_at, :finished_at, :winner)
                    ON CONFLICT (id) DO UPDATE SET
                        id = :id,
                        reference = :reference,
                        name = :name,
                        competition_type = :competition_type,
                        users = :users,
                        description = :description,
                        created_at = :created_at,
                        started_at = :started_at,
                        finished_at = :finished_at,
                        winner = :winner
                    ',
                self::TABLE,
            ),
        );

        $stmt->bindValue(':id', $competition->id()->value());
        $stmt->bindValue(':reference', $competition->reference());
        $stmt->bindValue(':name', $competition->name());
        $stmt->bindValue(':competition_type', $competition->type()->name);
        $stmt->bindValue(':users', Json::encode($competition->users()));
        $stmt->bindValue(':description', $competition->description());
        $stmt->bindValue(':created_at', $competition->createdAt()->format(\DateTimeInterface::ATOM));
        $stmt->bindValue(':started_at', $competition->startedAt()?->format(\DateTimeInterface::ATOM));
        $stmt->bindValue(':finished_at', $competition->finishedAt()?->format(\DateTimeInterface::ATOM));
        $stmt->bindValue(':winner', $competition->winner()?->value());

        $stmt->executeStatement();
    }
}
