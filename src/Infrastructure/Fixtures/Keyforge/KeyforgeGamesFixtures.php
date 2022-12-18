<?php declare(strict_types=1);

namespace AdnanMula\Cards\Infrastructure\Fixtures\Keyforge;

use AdnanMula\Cards\Domain\Model\Keyforge\KeyforgeGame;
use AdnanMula\Cards\Domain\Model\Keyforge\ValueObject\KeyforgeCompetition;
use AdnanMula\Cards\Domain\Model\Keyforge\ValueObject\KeyforgeGameScore;
use AdnanMula\Cards\Domain\Model\Shared\ValueObject\Uuid;
use AdnanMula\Cards\Domain\Service\Persistence\Fixture;
use AdnanMula\Cards\Infrastructure\Fixtures\DbalFixture;

final class KeyforgeGamesFixtures extends DbalFixture implements Fixture
{
    public const FIXTURE_KEYFORGE_GAME_1_ID = '1b5ba448-a1f3-436d-9fd5-2c4ec84109b3';
    public const FIXTURE_KEYFORGE_GAME_2_ID = '3e68a25d-907b-4d6f-bab8-9b445bd7b113';
    public const FIXTURE_KEYFORGE_GAME_3_ID = 'fa561709-58c1-4090-8089-ef2f4b721077';
    public const FIXTURE_KEYFORGE_GAME_4_ID = 'e972eacb-0271-4b0c-9905-b403e50ff3cf';
    public const FIXTURE_KEYFORGE_GAME_5_ID = '6d9b7423-0d9d-4e91-bf1d-b3a05e15ccca';
    public const FIXTURE_KEYFORGE_GAME_6_ID = '4ac03ade-d1f6-4743-81f5-aa5810380476';
    public const FIXTURE_KEYFORGE_GAME_7_ID = 'bd18c510-4f4a-4de2-a0db-8ccd5a4ab602';
    public const FIXTURE_KEYFORGE_GAME_8_ID = '18b41976-5096-4f71-b4bd-2da2a5984b59';
    public const FIXTURE_KEYFORGE_GAME_9_ID = 'c91e7ebf-5ef3-4c54-9ffd-24487c1597ab';
    public const FIXTURE_KEYFORGE_GAME_10_ID = '11fdb28b-070c-4c8b-8b2b-57d4431045f7';
    public const FIXTURE_KEYFORGE_GAME_11_ID = 'fa894952-3e79-43ad-aa83-69ebecf213fc';
    public const FIXTURE_KEYFORGE_GAME_12_ID = '5da99bd0-563a-4247-8d04-8a904c8542ab';
    public const FIXTURE_KEYFORGE_GAME_13_ID = 'a4e32bc2-f3ed-4c56-a742-e8d75dd2acb3';

    private const TABLE = 'keyforge_games';

    private bool $loaded = false;

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
                KeyforgeCompetition::NKFL_LEAGUE_SEASON_19,
                '',
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
                KeyforgeCompetition::NKFL_LEAGUE_SEASON_19,
                '',
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
        $stmt = $this->connection->prepare(
            \sprintf(
                '
                    INSERT INTO %s (id, winner, loser, winner_deck, loser_deck, first_turn, score, date, created_at, winner_chains, loser_chains, competition, notes)
                    VALUES (:id, :winner, :loser, :winner_deck, :loser_deck, :first_turn, :score, :date, :created_at, :winner_chains, :loser_chains, :competition, :notes)
                    ON CONFLICT (id) DO UPDATE SET
                        id = :id,
                        winner = :winner,
                        loser = :loser,
                        winner_deck = :winner_deck,
                        loser_deck = :loser_deck,
                        first_turn = :first_turn,
                        score = :score,
                        date = :date,
                        created_at = :created_at,
                        winner_chains = :winner_chains,
                        loser_chains = :loser_chains,
                        competition = :competition,
                        notes = :notes
                    ',
                self::TABLE,
            ),
        );

        $stmt->bindValue(':id', $game->id()->value());
        $stmt->bindValue(':winner', $game->winner()->value());
        $stmt->bindValue(':loser', $game->loser()->value());
        $stmt->bindValue(':winner_deck', $game->winnerDeck()->value());
        $stmt->bindValue(':loser_deck', $game->loserDeck()->value());
        $stmt->bindValue(':winner_chains', $game->winnerChains());
        $stmt->bindValue(':loser_chains', $game->loserChains());
        $stmt->bindValue(':first_turn', $game->firstTurn()?->value());
        $stmt->bindValue(':score', \json_encode($game->score()));
        $stmt->bindValue(':date', $game->date()->format(\DateTimeInterface::ATOM));
        $stmt->bindValue(':created_at', $game->createdAt()->format(\DateTimeInterface::ATOM));
        $stmt->bindValue(':competition', $game->competition()->name);
        $stmt->bindValue(':notes', $game->notes());

        $stmt->execute();
    }
}
