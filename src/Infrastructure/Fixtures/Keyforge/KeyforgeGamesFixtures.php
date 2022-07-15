<?php declare(strict_types=1);

namespace AdnanMula\Cards\Infrastructure\Fixtures\Keyforge;

use AdnanMula\Cards\Domain\Model\Keyforge\KeyforgeDeck;
use AdnanMula\Cards\Domain\Model\Keyforge\KeyforgeGame;
use AdnanMula\Cards\Domain\Model\Keyforge\ValueObject\KeyforgeDeckHouses;
use AdnanMula\Cards\Domain\Model\Keyforge\ValueObject\KeyforgeGameScore;
use AdnanMula\Cards\Domain\Model\Keyforge\ValueObject\KeyforgeHouse;
use AdnanMula\Cards\Domain\Model\Keyforge\ValueObject\KeyforgeSet;
use AdnanMula\Cards\Domain\Model\Shared\ValueObject\Uuid;
use AdnanMula\Cards\Domain\Service\Persistence\Fixture;
use AdnanMula\Cards\Infrastructure\Fixtures\DbalFixture;
use AdnanMula\Cards\Infrastructure\Fixtures\User\UserFixtures;

final class KeyforgeGamesFixtures extends DbalFixture implements Fixture
{
    public const FIXTURE_KEYFORGE_GAME_1_ID = '1b5ba448-a1f3-436d-9fd5-2c4ec84109b3';
    public const FIXTURE_KEYFORGE_GAME_2_ID = '3e68a25d-907b-4d6f-bab8-9b445bd7b113';
    public const FIXTURE_KEYFORGE_GAME_3_ID = 'fa561709-58c1-4090-8089-ef2f4b721077';

    private const TABLE = 'keyforge_games';

    private bool $loaded = false;

    public function load(): void
    {
        $this->save(
            new KeyforgeGame(
                Uuid::from(self::FIXTURE_KEYFORGE_GAME_1_ID),
                Uuid::from(UserFixtures::FIXTURE_USER_2_ID),
                Uuid::from(UserFixtures::FIXTURE_USER_1_ID),
                Uuid::from(KeyforgeDecksFixtures::FIXTURE_KEYFORGE_DECK_1_ID),
                Uuid::from(KeyforgeDecksFixtures::FIXTURE_KEYFORGE_DECK_2_ID),
                Uuid::from(UserFixtures::FIXTURE_USER_2_ID),
                KeyforgeGameScore::from(3, 2),
                new \DateTimeImmutable('2022-05-24 16:00:00'),
            ),
        );

        $this->save(
            new KeyforgeGame(
                Uuid::from(self::FIXTURE_KEYFORGE_GAME_2_ID),
                Uuid::from(UserFixtures::FIXTURE_USER_1_ID),
                Uuid::from(UserFixtures::FIXTURE_USER_2_ID),
                Uuid::from(KeyforgeDecksFixtures::FIXTURE_KEYFORGE_DECK_3_ID),
                Uuid::from(KeyforgeDecksFixtures::FIXTURE_KEYFORGE_DECK_2_ID),
                Uuid::from(UserFixtures::FIXTURE_USER_1_ID),
                KeyforgeGameScore::from(3, 1),
                new \DateTimeImmutable('2022-06-14 10:00:00'),
            ),
        );

        $this->save(
            new KeyforgeGame(
                Uuid::from(self::FIXTURE_KEYFORGE_GAME_3_ID),
                Uuid::from(UserFixtures::FIXTURE_USER_2_ID),
                Uuid::from(UserFixtures::FIXTURE_USER_1_ID),
                Uuid::from(KeyforgeDecksFixtures::FIXTURE_KEYFORGE_DECK_4_ID),
                Uuid::from(KeyforgeDecksFixtures::FIXTURE_KEYFORGE_DECK_1_ID),
                Uuid::from(UserFixtures::FIXTURE_USER_1_ID),
                KeyforgeGameScore::from(3, 0),
                new \DateTimeImmutable('2022-07-01 08:44:00'),
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
            UserFixtures::class,
            KeyforgeDecksFixtures::class,
        ];
    }

    private function save(KeyforgeGame $game): void
    {
        $stmt = $this->connection->prepare(
            \sprintf(
                '
                    INSERT INTO %s (id, winner, loser, winner_deck, loser_deck, first_turn, score, date)
                    VALUES (:id, :winner, :loser, :winner_deck, :loser_deck, :first_turn, :score, :date)
                    ON CONFLICT (id) DO UPDATE SET
                        id = :id,
                        winner = :winner,
                        loser = :loser,
                        winner_deck = :winner_deck,
                        loser_deck = :loser_deck,
                        first_turn = :first_turn,
                        score = :score,
                        date = :date
                    ',
                self::TABLE,
            ),
        );

        $stmt->bindValue(':id', $game->id()->value());
        $stmt->bindValue(':winner', $game->winner()->value());
        $stmt->bindValue(':loser', $game->loser()->value());
        $stmt->bindValue(':winner_deck', $game->winnerDeck()->value());
        $stmt->bindValue(':loser_deck', $game->loserDeck()->value());
        $stmt->bindValue(':first_turn', null === $game->firstTurn() ? null : $game->firstTurn()->value());
        $stmt->bindValue(':score', \json_encode($game->score()));
        $stmt->bindValue(':date', $game->date()->format(\DateTimeInterface::ATOM));

        $stmt->execute();
    }
}
