<?php declare(strict_types=1);

namespace AdnanMula\Cards\Infrastructure\Fixtures\Keyforge;

use AdnanMula\Cards\Application\Service\Json;
use AdnanMula\Cards\Domain\Model\Keyforge\KeyforgeCompetitionFixture;
use AdnanMula\Cards\Domain\Model\Shared\ValueObject\CompetitionFixtureType;
use AdnanMula\Cards\Domain\Model\Shared\ValueObject\Uuid;
use AdnanMula\Cards\Domain\Service\Persistence\Fixture;
use AdnanMula\Cards\Infrastructure\Fixtures\DbalFixture;

final class KeyforgeCompetitionFixtureFixtures extends DbalFixture implements Fixture
{
    public const FIXTURE_KEYFORGE_COMPETITION_FIXTURE_1_ID = '4e3159be-8292-4ed3-a167-3236901ec626';
    public const FIXTURE_KEYFORGE_COMPETITION_FIXTURE_2_ID = '390142c9-ef8d-4229-ae30-063e9fa5d131';
    public const FIXTURE_KEYFORGE_COMPETITION_FIXTURE_3_ID = '43695a41-b7ff-45b3-9654-8253f830d92c';
    public const FIXTURE_KEYFORGE_COMPETITION_FIXTURE_4_ID = '54b1c40a-3ef4-4aad-9b67-a23a64fc5e16';
    public const FIXTURE_KEYFORGE_COMPETITION_FIXTURE_5_ID = '38c9f964-c2c4-4116-909c-55ee9bfd0052';
    public const FIXTURE_KEYFORGE_COMPETITION_FIXTURE_6_ID = '2229c2fb-e520-4b98-ab1d-ce493064c40d';

    private const TABLE = 'keyforge_competition_fixtures';

    private bool $loaded = false;

    public function load(): void
    {
        $this->save(
            new KeyforgeCompetitionFixture(
                Uuid::from(self::FIXTURE_KEYFORGE_COMPETITION_FIXTURE_1_ID),
                Uuid::from(KeyforgeCompetitionFixtures::FIXTURE_KEYFORGE_COMPETITION_2_ID),
                'Jornada 1',
                [
                    Uuid::from(KeyforgeUsersFixtures::FIXTURE_KF_USER_1_ID),
                    Uuid::from(KeyforgeUsersFixtures::FIXTURE_KF_USER_2_ID),
                ],
                CompetitionFixtureType::BEST_OF_1,
                0,
                new \DateTimeImmutable('2022-12-05'),
                new \DateTimeImmutable('2022-12-05'),
                Uuid::from(KeyforgeUsersFixtures::FIXTURE_KF_USER_2_ID),
                [
                    KeyforgeGamesFixtures::FIXTURE_KEYFORGE_GAME_14_ID,
                ],
            ),
        );

        $this->save(
            new KeyforgeCompetitionFixture(
                Uuid::from(self::FIXTURE_KEYFORGE_COMPETITION_FIXTURE_2_ID),
                Uuid::from(KeyforgeCompetitionFixtures::FIXTURE_KEYFORGE_COMPETITION_2_ID),
                'Jornada 1',
                [
                    Uuid::from(KeyforgeUsersFixtures::FIXTURE_KF_USER_1_ID),
                    Uuid::from(KeyforgeUsersFixtures::FIXTURE_KF_USER_3_ID),
                ],
                CompetitionFixtureType::BEST_OF_1,
                1,
                new \DateTimeImmutable('2022-12-05'),
                new \DateTimeImmutable('2022-12-05'),
                Uuid::from(KeyforgeUsersFixtures::FIXTURE_KF_USER_3_ID),
                [
                    KeyforgeGamesFixtures::FIXTURE_KEYFORGE_GAME_15_ID,
                ],
            ),
        );

        $this->save(
            new KeyforgeCompetitionFixture(
                Uuid::from(self::FIXTURE_KEYFORGE_COMPETITION_FIXTURE_3_ID),
                Uuid::from(KeyforgeCompetitionFixtures::FIXTURE_KEYFORGE_COMPETITION_2_ID),
                'Jornada 1',
                [
                    Uuid::from(KeyforgeUsersFixtures::FIXTURE_KF_USER_2_ID),
                    Uuid::from(KeyforgeUsersFixtures::FIXTURE_KF_USER_3_ID),
                ],
                CompetitionFixtureType::BEST_OF_1,
                2,
                new \DateTimeImmutable('2022-12-05'),
                new \DateTimeImmutable('2022-12-05'),
                Uuid::from(KeyforgeUsersFixtures::FIXTURE_KF_USER_3_ID),
                [
                    KeyforgeGamesFixtures::FIXTURE_KEYFORGE_GAME_16_ID,
                ],
            ),
        );

        $this->save(
            new KeyforgeCompetitionFixture(
                Uuid::from(self::FIXTURE_KEYFORGE_COMPETITION_FIXTURE_4_ID),
                Uuid::from(KeyforgeCompetitionFixtures::FIXTURE_KEYFORGE_COMPETITION_1_ID),
                'Jornada 1',
                [
                    Uuid::from(KeyforgeUsersFixtures::FIXTURE_KF_USER_1_ID),
                    Uuid::from(KeyforgeUsersFixtures::FIXTURE_KF_USER_2_ID),
                ],
                CompetitionFixtureType::BEST_OF_1,
                0,
                new \DateTimeImmutable('2022-12-05'),
                new \DateTimeImmutable('2022-12-05'),
                null,
                [],
            ),
        );

        $this->save(
            new KeyforgeCompetitionFixture(
                Uuid::from(self::FIXTURE_KEYFORGE_COMPETITION_FIXTURE_5_ID),
                Uuid::from(KeyforgeCompetitionFixtures::FIXTURE_KEYFORGE_COMPETITION_1_ID),
                'Jornada 1',
                [
                    Uuid::from(KeyforgeUsersFixtures::FIXTURE_KF_USER_1_ID),
                    Uuid::from(KeyforgeUsersFixtures::FIXTURE_KF_USER_3_ID),
                ],
                CompetitionFixtureType::BEST_OF_1,
                1,
                new \DateTimeImmutable('2022-12-05'),
                new \DateTimeImmutable('2022-12-05'),
                null,
                [],
            ),
        );

        $this->save(
            new KeyforgeCompetitionFixture(
                Uuid::from(self::FIXTURE_KEYFORGE_COMPETITION_FIXTURE_6_ID),
                Uuid::from(KeyforgeCompetitionFixtures::FIXTURE_KEYFORGE_COMPETITION_1_ID),
                'Jornada 1',
                [
                    Uuid::from(KeyforgeUsersFixtures::FIXTURE_KF_USER_2_ID),
                    Uuid::from(KeyforgeUsersFixtures::FIXTURE_KF_USER_3_ID),
                ],
                CompetitionFixtureType::BEST_OF_1,
                2,
                new \DateTimeImmutable('2022-12-05'),
                new \DateTimeImmutable('2022-12-05'),
                null,
                [],
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

    public function save(KeyforgeCompetitionFixture $fixture): void
    {
        $stmt = $this->connection->prepare(
            \sprintf(
                '
                    INSERT INTO %s (id, competition_id, reference, users, fixture_type, position, created_at, played_at, winner, games)
                    VALUES (:id, :competition_id, :reference, :users, :fixture_type, :position, :created_at, :played_at, :winner, :games)
                    ON CONFLICT (id) DO UPDATE SET
                        competition_id = :competition_id,
                        reference = :reference,
                        users = :users,
                        fixture_type = :fixture_type,
                        position = :position,
                        created_at = :created_at,
                        played_at = :played_at,
                        winner = :winner,
                        games = :games
                    ',
                self::TABLE,
            ),
        );

        $stmt->bindValue(':id', $fixture->id()->value());
        $stmt->bindValue(':competition_id', $fixture->competitionId()->value());
        $stmt->bindValue(':reference', $fixture->reference());
        $stmt->bindValue(':users', Json::encode($fixture->users()));
        $stmt->bindValue(':fixture_type', $fixture->type()->name);
        $stmt->bindValue(':position', $fixture->position());
        $stmt->bindValue(':created_at', $fixture->createdAt()->format(\DateTimeInterface::ATOM));
        $stmt->bindValue(':played_at', $fixture->playedAt()?->format('Y-m-d'));
        $stmt->bindValue(':winner', $fixture->winner()?->value());
        $stmt->bindValue(':games', Json::encode($fixture->games()));

        $stmt->execute();
    }
}
