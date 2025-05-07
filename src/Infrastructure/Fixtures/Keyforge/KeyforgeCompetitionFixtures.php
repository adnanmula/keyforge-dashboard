<?php declare(strict_types=1);

namespace AdnanMula\Cards\Infrastructure\Fixtures\Keyforge;

use AdnanMula\Cards\Domain\Model\Keyforge\Game\KeyforgeCompetition;
use AdnanMula\Cards\Domain\Model\Shared\ValueObject\CompetitionVisibility;
use AdnanMula\Cards\Domain\Model\Shared\ValueObject\Uuid;
use AdnanMula\Cards\Domain\Service\Persistence\Fixture;
use AdnanMula\Cards\Infrastructure\Fixtures\DbalFixture;
use AdnanMula\Cards\Infrastructure\Persistence\Repository\Keyforge\Game\KeyforgeCompetitionDbalRepository;
use AdnanMula\Tournament\Classification\Classification;
use AdnanMula\Tournament\Fixture\Fixtures;
use AdnanMula\Tournament\Fixture\FixtureType;
use AdnanMula\Tournament\TournamentType;
use AdnanMula\Tournament\User;
use Doctrine\DBAL\Connection;

final class KeyforgeCompetitionFixtures extends DbalFixture implements Fixture
{
    public const string FIXTURE_KEYFORGE_COMPETITION_1_ID = 'dbeec29a-d227-45b4-9c00-709c550817a7';
    public const string FIXTURE_KEYFORGE_COMPETITION_2_ID = 'c7dfd4c4-9351-4f0f-ba2f-ca5d6b0f57e1';

    private bool $loaded = false;

    public function __construct(Connection $connection, private KeyforgeCompetitionDbalRepository $competitionRepository)
    {
        parent::__construct($connection);
    }

    public function load(): void
    {
        $this->competitionRepository->save(
            new KeyforgeCompetition(
                Uuid::from(self::FIXTURE_KEYFORGE_COMPETITION_1_ID),
                'Torneo1',
                'Description',
                TournamentType::ROUND_ROBIN_1,
                [
                    new User(KeyforgeUsersFixtures::FIXTURE_KF_USER_1_ID, '1'),
                ],
                [
                    new User(KeyforgeUsersFixtures::FIXTURE_KF_USER_1_ID, '1'),
                    new User(KeyforgeUsersFixtures::FIXTURE_KF_USER_2_ID, '2'),
                    new User(KeyforgeUsersFixtures::FIXTURE_KF_USER_3_ID, '3'),
                ],
                new \DateTimeImmutable('2022-12-03'),
                null,
                null,
                CompetitionVisibility::FRIENDS,
                null,
                new Fixtures(FixtureType::BEST_OF_1, 'Jornada'),
                new Classification(false),
            ),
        );

        $this->competitionRepository->save(
            new KeyforgeCompetition(
                Uuid::from(self::FIXTURE_KEYFORGE_COMPETITION_2_ID),
                'Torneo2',
                'Description 2',
                TournamentType::ROUND_ROBIN_1,
                [
                    new User(KeyforgeUsersFixtures::FIXTURE_KF_USER_1_ID, '1'),
                ],
                [
                    new User(KeyforgeUsersFixtures::FIXTURE_KF_USER_1_ID, '1'),
                    new User(KeyforgeUsersFixtures::FIXTURE_KF_USER_2_ID, '2'),
                    new User(KeyforgeUsersFixtures::FIXTURE_KF_USER_3_ID, '3'),
                ],
                new \DateTimeImmutable('2022-12-03'),
                new \DateTimeImmutable('2022-12-14'),
                new \DateTimeImmutable('2022-12-23'),
                CompetitionVisibility::FRIENDS,
                Uuid::from(KeyforgeUsersFixtures::FIXTURE_KF_USER_1_ID),
                new Fixtures(FixtureType::BEST_OF_1, 'Jornada'),
                new Classification(true),
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
