<?php declare(strict_types=1);

namespace AdnanMula\Cards\Infrastructure\Fixtures\Keyforge;

use AdnanMula\Cards\Application\Service\Deck\UpdateDeckWinRateService;
use AdnanMula\Cards\Domain\Model\Keyforge\Game\KeyforgeGame;
use AdnanMula\Cards\Domain\Model\Keyforge\Game\KeyforgeGameRepository;
use AdnanMula\Cards\Domain\Model\Keyforge\Game\ValueObject\KeyforgeCompetition;
use AdnanMula\Cards\Domain\Model\Keyforge\Game\ValueObject\KeyforgeGameScore;
use AdnanMula\Cards\Domain\Model\Shared\ValueObject\Uuid;
use AdnanMula\Cards\Domain\Service\Persistence\Fixture;
use AdnanMula\Cards\Infrastructure\Fixtures\DbalFixture;
use Doctrine\DBAL\Connection;

final class KeyforgeGamesFixtures extends DbalFixture implements Fixture
{
    public const string FIXTURE_KEYFORGE_GAME_1_ID = '1b5ba448-a1f3-436d-9fd5-2c4ec84109b3';
    public const string FIXTURE_KEYFORGE_GAME_2_ID = '3e68a25d-907b-4d6f-bab8-9b445bd7b113';
    public const string FIXTURE_KEYFORGE_GAME_3_ID = 'fa561709-58c1-4090-8089-ef2f4b721077';
    public const string FIXTURE_KEYFORGE_GAME_4_ID = 'e972eacb-0271-4b0c-9905-b403e50ff3cf';
    public const string FIXTURE_KEYFORGE_GAME_5_ID = '6d9b7423-0d9d-4e91-bf1d-b3a05e15ccca';
    public const string FIXTURE_KEYFORGE_GAME_6_ID = '4ac03ade-d1f6-4743-81f5-aa5810380476';
    public const string FIXTURE_KEYFORGE_GAME_7_ID = 'bd18c510-4f4a-4de2-a0db-8ccd5a4ab602';
    public const string FIXTURE_KEYFORGE_GAME_8_ID = '18b41976-5096-4f71-b4bd-2da2a5984b59';
    public const string FIXTURE_KEYFORGE_GAME_9_ID = 'c91e7ebf-5ef3-4c54-9ffd-24487c1597ab';
    public const string FIXTURE_KEYFORGE_GAME_10_ID = '11fdb28b-070c-4c8b-8b2b-57d4431045f7';
    public const string FIXTURE_KEYFORGE_GAME_11_ID = 'fa894952-3e79-43ad-aa83-69ebecf213fc';
    public const string FIXTURE_KEYFORGE_GAME_12_ID = '5da99bd0-563a-4247-8d04-8a904c8542ab';
    public const string FIXTURE_KEYFORGE_GAME_13_ID = 'a4e32bc2-f3ed-4c56-a742-e8d75dd2acb3';
    public const string FIXTURE_KEYFORGE_GAME_14_ID = '99ab217f-bb7f-47f4-a567-4895c2550b12';
    public const string FIXTURE_KEYFORGE_GAME_15_ID = 'efa54d26-a5a7-4b1e-8719-03ce61c95867';
    public const string FIXTURE_KEYFORGE_GAME_16_ID = '71fa478b-df90-4563-924c-c29636afacaf';

    private bool $loaded = false;

    public function __construct(
        Connection $connection,
        private readonly KeyforgeGameRepository $repository,
        private readonly UpdateDeckWinRateService $updateDeckWinRateService,
    ) {
        parent::__construct($connection);
    }

    public function load(): void
    {
        $this->save(
            new KeyforgeGame(
                Uuid::from(self::FIXTURE_KEYFORGE_GAME_1_ID),
                Uuid::from(KeyforgeUsersFixtures::FIXTURE_KF_USER_2_ID),
                Uuid::from(KeyforgeUsersFixtures::FIXTURE_KF_USER_1_ID),
                Uuid::from(KeyforgeDecksFixtures::FIXTURE_KEYFORGE_DECK_1_ID),
                Uuid::from(KeyforgeDecksFixtures::FIXTURE_KEYFORGE_DECK_2_ID),
                0,
                0,
                Uuid::from(KeyforgeUsersFixtures::FIXTURE_KF_USER_2_ID),
                KeyforgeGameScore::from(3, 2),
                new \DateTimeImmutable('2022-05-24 16:00:00'),
                new \DateTimeImmutable('2022-05-24 16:00:00'),
                KeyforgeCompetition::FRIENDS,
                '',
                true,
                null,
                null,
            ),
        );

        $this->save(
            new KeyforgeGame(
                Uuid::from(self::FIXTURE_KEYFORGE_GAME_2_ID),
                Uuid::from(KeyforgeUsersFixtures::FIXTURE_KF_USER_1_ID),
                Uuid::from(KeyforgeUsersFixtures::FIXTURE_KF_USER_2_ID),
                Uuid::from(KeyforgeDecksFixtures::FIXTURE_KEYFORGE_DECK_3_ID),
                Uuid::from(KeyforgeDecksFixtures::FIXTURE_KEYFORGE_DECK_2_ID),
                0,
                0,
                Uuid::from(KeyforgeUsersFixtures::FIXTURE_KF_USER_1_ID),
                KeyforgeGameScore::from(3, 1),
                new \DateTimeImmutable('2022-06-14 10:00:00'),
                new \DateTimeImmutable('2022-06-14 10:00:00'),
                KeyforgeCompetition::FRIENDS,
                '',
                true,
                null,
                null,
            ),
        );

        $this->save(
            new KeyforgeGame(
                Uuid::from(self::FIXTURE_KEYFORGE_GAME_3_ID),
                Uuid::from(KeyforgeUsersFixtures::FIXTURE_KF_USER_2_ID),
                Uuid::from(KeyforgeUsersFixtures::FIXTURE_KF_USER_1_ID),
                Uuid::from(KeyforgeDecksFixtures::FIXTURE_KEYFORGE_DECK_4_ID),
                Uuid::from(KeyforgeDecksFixtures::FIXTURE_KEYFORGE_DECK_1_ID),
                0,
                1,
                Uuid::from(KeyforgeUsersFixtures::FIXTURE_KF_USER_1_ID),
                KeyforgeGameScore::from(3, 0),
                new \DateTimeImmutable('2022-07-01 08:44:00'),
                new \DateTimeImmutable('2022-07-01 08:44:00'),
                KeyforgeCompetition::FRIENDS,
                '',
                true,
                null,
                null,
            ),
        );

        $this->save(
            new KeyforgeGame(
                Uuid::from(self::FIXTURE_KEYFORGE_GAME_4_ID),
                Uuid::from(KeyforgeUsersFixtures::FIXTURE_KF_USER_1_ID),
                Uuid::from(KeyforgeUsersFixtures::FIXTURE_KF_USER_2_ID),
                Uuid::from(KeyforgeDecksFixtures::FIXTURE_KEYFORGE_DECK_1_ID),
                Uuid::from(KeyforgeDecksFixtures::FIXTURE_KEYFORGE_DECK_4_ID),
                0,
                0,
                Uuid::from(KeyforgeUsersFixtures::FIXTURE_KF_USER_2_ID),
                KeyforgeGameScore::from(3, 2),
                new \DateTimeImmutable('2022-07-02 00:00:00'),
                new \DateTimeImmutable('2022-07-02 08:44:00'),
                KeyforgeCompetition::FRIENDS,
                '',
                true,
                null,
                null,
            ),
        );

        $this->save(
            new KeyforgeGame(
                Uuid::from(self::FIXTURE_KEYFORGE_GAME_5_ID),
                Uuid::from(KeyforgeUsersFixtures::FIXTURE_KF_USER_1_ID),
                Uuid::from(KeyforgeUsersFixtures::FIXTURE_KF_USER_2_ID),
                Uuid::from(KeyforgeDecksFixtures::FIXTURE_KEYFORGE_DECK_6_ID),
                Uuid::from(KeyforgeDecksFixtures::FIXTURE_KEYFORGE_DECK_7_ID),
                0,
                0,
                Uuid::from(KeyforgeUsersFixtures::FIXTURE_KF_USER_1_ID),
                KeyforgeGameScore::from(3, 1),
                new \DateTimeImmutable('2022-07-03 00:00:00'),
                new \DateTimeImmutable('2022-07-03 08:15:00'),
                KeyforgeCompetition::FRIENDS,
                '',
                true,
                null,
                null,
            ),
        );

        $this->save(
            new KeyforgeGame(
                Uuid::from(self::FIXTURE_KEYFORGE_GAME_6_ID),
                Uuid::from(KeyforgeUsersFixtures::FIXTURE_KF_USER_1_ID),
                Uuid::from(KeyforgeUsersFixtures::FIXTURE_KF_USER_2_ID),
                Uuid::from(KeyforgeDecksFixtures::FIXTURE_KEYFORGE_DECK_6_ID),
                Uuid::from(KeyforgeDecksFixtures::FIXTURE_KEYFORGE_DECK_7_ID),
                0,
                0,
                Uuid::from(KeyforgeUsersFixtures::FIXTURE_KF_USER_1_ID),
                KeyforgeGameScore::from(3, 1),
                new \DateTimeImmutable('2022-07-11 00:00:00'),
                new \DateTimeImmutable('2022-07-11 02:00:00'),
                KeyforgeCompetition::FRIENDS,
                '',
                true,
                null,
                null,
            ),
        );

        $this->save(
            new KeyforgeGame(
                Uuid::from(self::FIXTURE_KEYFORGE_GAME_7_ID),
                Uuid::from(KeyforgeUsersFixtures::FIXTURE_KF_USER_1_ID),
                Uuid::from(KeyforgeUsersFixtures::FIXTURE_KF_USER_2_ID),
                Uuid::from(KeyforgeDecksFixtures::FIXTURE_KEYFORGE_DECK_3_ID),
                Uuid::from(KeyforgeDecksFixtures::FIXTURE_KEYFORGE_DECK_2_ID),
                0,
                0,
                Uuid::from(KeyforgeUsersFixtures::FIXTURE_KF_USER_2_ID),
                KeyforgeGameScore::from(3, 2),
                new \DateTimeImmutable('2022-07-12 00:00:00'),
                new \DateTimeImmutable('2022-07-12 08:00:00'),
                KeyforgeCompetition::FRIENDS,
                '',
                true,
                null,
                null,
            ),
        );


        $this->save(
            new KeyforgeGame(
                Uuid::from(self::FIXTURE_KEYFORGE_GAME_8_ID),
                Uuid::from(KeyforgeUsersFixtures::FIXTURE_KF_USER_1_ID),
                Uuid::from(KeyforgeUsersFixtures::FIXTURE_KF_USER_2_ID),
                Uuid::from(KeyforgeDecksFixtures::FIXTURE_KEYFORGE_DECK_5_ID),
                Uuid::from(KeyforgeDecksFixtures::FIXTURE_KEYFORGE_DECK_7_ID),
                0,
                0,
                Uuid::from(KeyforgeUsersFixtures::FIXTURE_KF_USER_2_ID),
                KeyforgeGameScore::from(3, 2),
                new \DateTimeImmutable('2022-07-03 00:00:00'),
                new \DateTimeImmutable('2022-07-03 08:45:00'),
                KeyforgeCompetition::FRIENDS,
                '',
                true,
                null,
                null,
            ),
        );

        $this->save(
            new KeyforgeGame(
                Uuid::from(self::FIXTURE_KEYFORGE_GAME_9_ID),
                Uuid::from(KeyforgeUsersFixtures::FIXTURE_KF_USER_1_ID),
                Uuid::from(KeyforgeUsersFixtures::FIXTURE_KF_USER_2_ID),
                Uuid::from(KeyforgeDecksFixtures::FIXTURE_KEYFORGE_DECK_3_ID),
                Uuid::from(KeyforgeDecksFixtures::FIXTURE_KEYFORGE_DECK_1_ID),
                0,
                0,
                Uuid::from(KeyforgeUsersFixtures::FIXTURE_KF_USER_2_ID),
                KeyforgeGameScore::from(3, 2),
                new \DateTimeImmutable('2022-07-03 00:00:00'),
                new \DateTimeImmutable('2022-07-03 08:45:00'),
                KeyforgeCompetition::FRIENDS,
                '',
                true,
                null,
                null,
            ),
        );

        $this->save(
            new KeyforgeGame(
                Uuid::from(self::FIXTURE_KEYFORGE_GAME_10_ID),
                Uuid::from(KeyforgeUsersFixtures::FIXTURE_KF_USER_2_ID),
                Uuid::from(KeyforgeUsersFixtures::FIXTURE_KF_USER_1_ID),
                Uuid::from(KeyforgeDecksFixtures::FIXTURE_KEYFORGE_DECK_4_ID),
                Uuid::from(KeyforgeDecksFixtures::FIXTURE_KEYFORGE_DECK_5_ID),
                0,
                0,
                Uuid::from(KeyforgeUsersFixtures::FIXTURE_KF_USER_2_ID),
                KeyforgeGameScore::from(3, 2),
                new \DateTimeImmutable('2022-07-03 00:00:00'),
                new \DateTimeImmutable('2022-07-03 08:45:00'),
                KeyforgeCompetition::FRIENDS,
                '',
                true,
                null,
                null,
            ),
        );

        $this->save(
            new KeyforgeGame(
                Uuid::from(self::FIXTURE_KEYFORGE_GAME_11_ID),
                Uuid::from(KeyforgeUsersFixtures::FIXTURE_KF_USER_1_ID),
                Uuid::from(KeyforgeUsersFixtures::FIXTURE_KF_USER_2_ID),
                Uuid::from(KeyforgeDecksFixtures::FIXTURE_KEYFORGE_DECK_2_ID),
                Uuid::from(KeyforgeDecksFixtures::FIXTURE_KEYFORGE_DECK_1_ID),
                0,
                0,
                Uuid::from(KeyforgeUsersFixtures::FIXTURE_KF_USER_2_ID),
                KeyforgeGameScore::from(3, 2),
                new \DateTimeImmutable('2022-07-23 00:00:00'),
                new \DateTimeImmutable('2022-07-23 16:01:00'),
                KeyforgeCompetition::FRIENDS,
                '',
                true,
                null,
                null,
            ),
        );

        $this->save(
            new KeyforgeGame(
                Uuid::from(self::FIXTURE_KEYFORGE_GAME_12_ID),
                Uuid::from(KeyforgeUsersFixtures::FIXTURE_KF_USER_1_ID),
                Uuid::from(KeyforgeUsersFixtures::FIXTURE_KF_USER_2_ID),
                Uuid::from(KeyforgeDecksFixtures::FIXTURE_KEYFORGE_DECK_7_ID),
                Uuid::from(KeyforgeDecksFixtures::FIXTURE_KEYFORGE_DECK_4_ID),
                0,
                0,
                Uuid::from(KeyforgeUsersFixtures::FIXTURE_KF_USER_2_ID),
                KeyforgeGameScore::from(3, 1),
                new \DateTimeImmutable('2022-07-23 00:00:00'),
                new \DateTimeImmutable('2022-07-23 16:01:00'),
                KeyforgeCompetition::NKFL,
                '',
                true,
                null,
                null,
            ),
        );

        $this->save(
            new KeyforgeGame(
                Uuid::from(self::FIXTURE_KEYFORGE_GAME_13_ID),
                Uuid::from(KeyforgeUsersFixtures::FIXTURE_KF_USER_2_ID),
                Uuid::from(KeyforgeUsersFixtures::FIXTURE_KF_USER_3_ID),
                Uuid::from(KeyforgeDecksFixtures::FIXTURE_KEYFORGE_DECK_4_ID),
                Uuid::from(KeyforgeDecksFixtures::FIXTURE_KEYFORGE_DECK_1_ID),
                0,
                0,
                Uuid::from(KeyforgeUsersFixtures::FIXTURE_KF_USER_2_ID),
                KeyforgeGameScore::from(3, 1),
                new \DateTimeImmutable('2022-07-23 00:00:00'),
                new \DateTimeImmutable('2022-07-23 16:41:00'),
                KeyforgeCompetition::NKFL,
                '',
                true,
                null,
                null,
            ),
        );

        $this->save(
            new KeyforgeGame(
                Uuid::from(self::FIXTURE_KEYFORGE_GAME_14_ID),
                Uuid::from(KeyforgeUsersFixtures::FIXTURE_KF_USER_1_ID),
                Uuid::from(KeyforgeUsersFixtures::FIXTURE_KF_USER_2_ID),
                Uuid::from(KeyforgeDecksFixtures::FIXTURE_KEYFORGE_DECK_4_ID),
                Uuid::from(KeyforgeDecksFixtures::FIXTURE_KEYFORGE_DECK_1_ID),
                0,
                0,
                Uuid::from(KeyforgeUsersFixtures::FIXTURE_KF_USER_2_ID),
                KeyforgeGameScore::from(3, 1),
                new \DateTimeImmutable('2022-07-23 00:00:00'),
                new \DateTimeImmutable('2022-07-23 16:41:00'),
                KeyforgeCompetition::LOCAL_LEAGUE,
                'Torneo 1 | Jornada 1',
                true,
                null,
                null,
            ),
        );

        $this->save(
            new KeyforgeGame(
                Uuid::from(self::FIXTURE_KEYFORGE_GAME_15_ID),
                Uuid::from(KeyforgeUsersFixtures::FIXTURE_KF_USER_1_ID),
                Uuid::from(KeyforgeUsersFixtures::FIXTURE_KF_USER_3_ID),
                Uuid::from(KeyforgeDecksFixtures::FIXTURE_KEYFORGE_DECK_4_ID),
                Uuid::from(KeyforgeDecksFixtures::FIXTURE_KEYFORGE_DECK_1_ID),
                0,
                0,
                Uuid::from(KeyforgeUsersFixtures::FIXTURE_KF_USER_1_ID),
                KeyforgeGameScore::from(3, 1),
                new \DateTimeImmutable('2022-07-23 00:00:00'),
                new \DateTimeImmutable('2022-07-23 16:41:00'),
                KeyforgeCompetition::LOCAL_LEAGUE,
                'Torneo 1 | Jornada 2',
                true,
                null,
                null,
            ),
        );

        $this->save(
            new KeyforgeGame(
                Uuid::from(self::FIXTURE_KEYFORGE_GAME_16_ID),
                Uuid::from(KeyforgeUsersFixtures::FIXTURE_KF_USER_2_ID),
                Uuid::from(KeyforgeUsersFixtures::FIXTURE_KF_USER_3_ID),
                Uuid::from(KeyforgeDecksFixtures::FIXTURE_KEYFORGE_DECK_4_ID),
                Uuid::from(KeyforgeDecksFixtures::FIXTURE_KEYFORGE_DECK_1_ID),
                0,
                0,
                Uuid::from(KeyforgeUsersFixtures::FIXTURE_KF_USER_3_ID),
                KeyforgeGameScore::from(3, 1),
                new \DateTimeImmutable('2022-07-23 00:00:00'),
                new \DateTimeImmutable('2022-07-23 16:41:00'),
                KeyforgeCompetition::LOCAL_LEAGUE,
                'Torneo 1 | Jornada 3',
                true,
                null,
                null,
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
            KeyforgeDecksFixtures::class,
        ];
    }

    private function save(KeyforgeGame $game): void
    {
        $this->repository->save($game);
        $this->updateDeckWinRateService->execute($game->winnerDeck());
        $this->updateDeckWinRateService->execute($game->loserDeck());
    }
}
