<?php declare(strict_types=1);

namespace AdnanMula\Cards\Infrastructure\Fixtures\Keyforge;

use AdnanMula\Cards\Application\Service\Json;
use AdnanMula\Cards\Domain\Model\Keyforge\Game\KeyforgeCompetitionFixture;
use AdnanMula\Cards\Domain\Model\Keyforge\Game\KeyforgeCompetitionRepository;
use AdnanMula\Cards\Domain\Model\Shared\ValueObject\Uuid;
use AdnanMula\Cards\Domain\Service\Persistence\Fixture;
use AdnanMula\Cards\Infrastructure\Fixtures\DbalFixture;
use AdnanMula\Tournament\Fixture\FixtureType;
use Doctrine\DBAL\Connection;

final class KeyforgeCompetitionFixtureFixtures extends DbalFixture implements Fixture
{
    public const string FIXTURE_KEYFORGE_COMPETITION_FIXTURE_1_ID = '4e3159be-8292-4ed3-a167-3236901ec626';
    public const string FIXTURE_KEYFORGE_COMPETITION_FIXTURE_2_ID = '390142c9-ef8d-4229-ae30-063e9fa5d131';
    public const string FIXTURE_KEYFORGE_COMPETITION_FIXTURE_3_ID = '43695a41-b7ff-45b3-9654-8253f830d92c';
    public const string FIXTURE_KEYFORGE_COMPETITION_FIXTURE_4_ID = '54b1c40a-3ef4-4aad-9b67-a23a64fc5e16';
    public const string FIXTURE_KEYFORGE_COMPETITION_FIXTURE_5_ID = '38c9f964-c2c4-4116-909c-55ee9bfd0052';
    public const string FIXTURE_KEYFORGE_COMPETITION_FIXTURE_6_ID = '2229c2fb-e520-4b98-ab1d-ce493064c40d';

    private const string TABLE = 'keyforge_competition_fixtures';

    private bool $loaded = false;

    public function __construct(Connection $connection, private KeyforgeCompetitionRepository $competitionRepository)
    {
        parent::__construct($connection);
    }

    public function load(): void
    {
        $this->competitionRepository->saveFixture(
            new KeyforgeCompetitionFixture(
                Uuid::from(self::FIXTURE_KEYFORGE_COMPETITION_FIXTURE_1_ID),
                Uuid::from(KeyforgeCompetitionFixtures::FIXTURE_KEYFORGE_COMPETITION_2_ID),
                Uuid::from(KeyforgeUsersFixtures::FIXTURE_KF_USER_2_ID),
                [
                    KeyforgeGamesFixtures::FIXTURE_KEYFORGE_GAME_14_ID,
                ],
                'Jornada 1',
                [
                    Uuid::from(KeyforgeUsersFixtures::FIXTURE_KF_USER_1_ID),
                    Uuid::from(KeyforgeUsersFixtures::FIXTURE_KF_USER_2_ID),
                ],
                FixtureType::BEST_OF_1,
                0,
                new \DateTimeImmutable('2022-12-05'),
                new \DateTimeImmutable('2022-12-05'),
            ),
        );

        $this->competitionRepository->saveFixture(
            new KeyforgeCompetitionFixture(
                Uuid::from(self::FIXTURE_KEYFORGE_COMPETITION_FIXTURE_2_ID),
                Uuid::from(KeyforgeCompetitionFixtures::FIXTURE_KEYFORGE_COMPETITION_2_ID),
                Uuid::from(KeyforgeUsersFixtures::FIXTURE_KF_USER_3_ID),
                [
                    KeyforgeGamesFixtures::FIXTURE_KEYFORGE_GAME_15_ID,
                ],
                'Jornada 1',
                [
                    Uuid::from(KeyforgeUsersFixtures::FIXTURE_KF_USER_1_ID),
                    Uuid::from(KeyforgeUsersFixtures::FIXTURE_KF_USER_3_ID),
                ],
                FixtureType::BEST_OF_1,
                1,
                new \DateTimeImmutable('2022-12-05'),
                new \DateTimeImmutable('2022-12-05'),
            ),
        );

        $this->competitionRepository->saveFixture(
            new KeyforgeCompetitionFixture(
                Uuid::from(self::FIXTURE_KEYFORGE_COMPETITION_FIXTURE_3_ID),
                Uuid::from(KeyforgeCompetitionFixtures::FIXTURE_KEYFORGE_COMPETITION_2_ID),
                Uuid::from(KeyforgeUsersFixtures::FIXTURE_KF_USER_3_ID),
                [
                    KeyforgeGamesFixtures::FIXTURE_KEYFORGE_GAME_16_ID,
                ],
                'Jornada 1',
                [
                    Uuid::from(KeyforgeUsersFixtures::FIXTURE_KF_USER_2_ID),
                    Uuid::from(KeyforgeUsersFixtures::FIXTURE_KF_USER_3_ID),
                ],
                FixtureType::BEST_OF_1,
                2,
                new \DateTimeImmutable('2022-12-05'),
                new \DateTimeImmutable('2022-12-05'),
            ),
        );

        $this->competitionRepository->saveFixture(
            new KeyforgeCompetitionFixture(
                Uuid::from(self::FIXTURE_KEYFORGE_COMPETITION_FIXTURE_4_ID),
                Uuid::from(KeyforgeCompetitionFixtures::FIXTURE_KEYFORGE_COMPETITION_1_ID),
                null,
                [],
                'Jornada 1',
                [
                    Uuid::from(KeyforgeUsersFixtures::FIXTURE_KF_USER_1_ID),
                    Uuid::from(KeyforgeUsersFixtures::FIXTURE_KF_USER_2_ID),
                ],
                FixtureType::BEST_OF_1,
                0,
                new \DateTimeImmutable('2022-12-05'),
                new \DateTimeImmutable('2022-12-05'),
            ),
        );

        $this->competitionRepository->saveFixture(
            new KeyforgeCompetitionFixture(
                Uuid::from(self::FIXTURE_KEYFORGE_COMPETITION_FIXTURE_5_ID),
                Uuid::from(KeyforgeCompetitionFixtures::FIXTURE_KEYFORGE_COMPETITION_1_ID),
                null,
                [],
                'Jornada 1',
                [
                    Uuid::from(KeyforgeUsersFixtures::FIXTURE_KF_USER_1_ID),
                    Uuid::from(KeyforgeUsersFixtures::FIXTURE_KF_USER_3_ID),
                ],
                FixtureType::BEST_OF_1,
                1,
                new \DateTimeImmutable('2022-12-05'),
                new \DateTimeImmutable('2022-12-05'),
            ),
        );

        $this->competitionRepository->saveFixture(
            new KeyforgeCompetitionFixture(
                Uuid::from(self::FIXTURE_KEYFORGE_COMPETITION_FIXTURE_6_ID),
                Uuid::from(KeyforgeCompetitionFixtures::FIXTURE_KEYFORGE_COMPETITION_1_ID),
                null,
                [],
                'Jornada 1',
                [
                    Uuid::from(KeyforgeUsersFixtures::FIXTURE_KF_USER_2_ID),
                    Uuid::from(KeyforgeUsersFixtures::FIXTURE_KF_USER_3_ID),
                ],
                FixtureType::BEST_OF_1,
                2,
                new \DateTimeImmutable('2022-12-05'),
                new \DateTimeImmutable('2022-12-05'),
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
}
