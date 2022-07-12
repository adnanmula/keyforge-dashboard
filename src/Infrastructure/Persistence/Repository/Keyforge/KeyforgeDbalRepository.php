<?php declare(strict_types=1);

namespace AdnanMula\Cards\Infrastructure\Persistence\Repository\Keyforge;

use AdnanMula\Cards\Domain\Model\Keyforge\KeyforgeDeck;
use AdnanMula\Cards\Domain\Model\Keyforge\KeyforgeGame;
use AdnanMula\Cards\Domain\Model\Keyforge\ValueObject\KeyforgeDeckHouses;
use AdnanMula\Cards\Domain\Model\Keyforge\ValueObject\KeyforgeGameScore;
use AdnanMula\Cards\Domain\Model\Keyforge\ValueObject\KeyforgeHouse;
use AdnanMula\Cards\Domain\Model\Keyforge\ValueObject\KeyforgeSet;
use AdnanMula\Cards\Domain\Model\Shared\ValueObject\UuidValueObject;
use AdnanMula\Cards\Domain\Model\Keyforge\KeyforgeRepository;
use AdnanMula\Cards\Infrastructure\Persistence\Repository\DbalRepository;

final class KeyforgeDbalRepository extends DbalRepository implements KeyforgeRepository
{
    private const TABLE = 'keyforge_decks';
    private const TABLE_GAMES = 'keyforge_games';

    public function all(int $page, int $pageSize): array
    {
        $result = $this->connection->createQueryBuilder()
            ->select('a.*')
            ->from(self::TABLE, 'a')
            ->execute()
            ->fetchAllAssociative();

        return \array_map(fn (array $row) => $this->map($row), $result);
    }

    public function byId(UuidValueObject $id): ?KeyforgeDeck
    {
        $result = $this->connection->createQueryBuilder()
            ->select('a.*')
            ->from(self::TABLE, 'a')
            ->where('a.id = :id')
            ->setParameter('id', $id->value())
            ->setMaxResults(1)
            ->execute()
            ->fetchAssociative();

        if (false === $result) {
            return null;
        }

        return $this->map($result);
    }

    public function save(KeyforgeDeck $deck): void
    {
        $stmt = $this->connection->prepare(
            \sprintf(
                '
                    INSERT INTO %s (id, name, set, houses, sas, wins, losses)
                    VALUES (:id, :name, :set, :houses, :sas, :wins, :losses)
                    ON CONFLICT (id) DO UPDATE SET
                        id = :id,
                        name = :name,
                        set = :set,
                        houses = :houses,
                        sas = :sas,
                        wins = :wins,
                        losses = :losses
                    ',
                self::TABLE,
            ),
        );

        $stmt->bindValue(':id', $deck->id()->value());
        $stmt->bindValue(':name', $deck->name());
        $stmt->bindValue(':set', $deck->set()->name);
        $stmt->bindValue(':houses', \json_encode($deck->houses()->value()));
        $stmt->bindValue(':sas', $deck->sas());
        $stmt->bindValue(':wins', $deck->wins());
        $stmt->bindValue(':losses', $deck->losses());

        $stmt->execute();
    }

    private function map(array $deck): KeyforgeDeck
    {
        return new KeyforgeDeck(
            UuidValueObject::from($deck['id']),
            $deck['name'],
            KeyforgeSet::from($deck['set']),
            KeyforgeDeckHouses::from(
                ...\array_map(
                    static fn (string $house) => KeyforgeHouse::from($house),
                    \json_decode($deck['houses'], true, 512, JSON_THROW_ON_ERROR),
                ),
            ),
            $deck['sas'],
            $deck['wins'],
            $deck['losses'],
        );
    }

    /** @return array<KeyforgeGame> */
    public function gamesByUser(UuidValueObject $id): array
    {
        $result = $this->connection->createQueryBuilder()
            ->select('a.*')
            ->from(self::TABLE_GAMES, 'a')
            ->where('a.winner = :id')
            ->orWhere('a.loser = :id')
            ->setParameter('id', $id->value())
            ->execute()
            ->fetchAllAssociative();

        if (false === $result) {
            return [];
        }

        return \array_map(fn (array $game) => $this->mapGame($game), $result);
    }

    /** @return array<KeyforgeGame> */
    public function gamesByDeck(UuidValueObject $id): array
    {
        $result = $this->connection->createQueryBuilder()
            ->select('a.*')
            ->from(self::TABLE_GAMES, 'a')
            ->where('a.winner_deck = :id')
            ->orWhere('a.winner_deck = :id')
            ->setParameter('id', $id->value())
            ->execute()
            ->fetchAllAssociative();

        if (false === $result) {
            return [];
        }

        return \array_map(fn (array $game) => $this->mapGame($game), $result);
    }

    private function mapGame(array $game): KeyforgeGame
    {
        $score = \json_decode($game['score'], true, 512, JSON_THROW_ON_ERROR);

        return new KeyforgeGame(
            UuidValueObject::from($game['id']),
            UuidValueObject::from($game['winner']),
            UuidValueObject::from($game['loser']),
            UuidValueObject::from($game['winner_deck']),
            UuidValueObject::from($game['loser_deck']),
            UuidValueObject::from($game['first_turn']),
            KeyforgeGameScore::from($score['winner_score'], $score['loser_score']),
            new \DateTimeImmutable($game['date']),
        );
    }
}
