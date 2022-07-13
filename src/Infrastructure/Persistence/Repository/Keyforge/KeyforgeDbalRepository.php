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
use Doctrine\DBAL\Connection;

final class KeyforgeDbalRepository extends DbalRepository implements KeyforgeRepository
{
    private const TABLE = 'keyforge_decks';
    private const TABLE_GAMES = 'keyforge_games';

    public function all(int $page, int $pageSize): array
    {
        $result = $this->connection->createQueryBuilder()
            ->select('a.*')
            ->from(self::TABLE, 'a')
            ->orderBy('a.wins', 'DESC')
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

    public function byIds(UuidValueObject ...$ids): array
    {
        $result = $this->connection->createQueryBuilder()
            ->select('a.*')
            ->from(self::TABLE, 'a')
            ->where('a.id in (:ids)')
            ->setParameter('ids', \array_map(static fn (UuidValueObject $id) => $id->value(), $ids), Connection::PARAM_STR_ARRAY)
            ->execute()
            ->fetchAllAssociative();

        if (false === $result) {
            return [];
        }

        return \array_map(fn (array $deck) => $this->map($deck), $result);
    }

    public function byNames(string ...$decks): array
    {
        $result = $this->connection->createQueryBuilder()
            ->select('a.*')
            ->from(self::TABLE, 'a')
            ->where('a.name in (:decks)')
            ->setParameter('decks', $decks, Connection::PARAM_STR_ARRAY)
            ->execute()
            ->fetchAllAssociative();

        if (false === $result) {
            return [];
        }

        return \array_map(fn (array $deck) => $this->map($deck), $result);
    }


    public function save(KeyforgeDeck $deck): void
    {
        $stmt = $this->connection->prepare(
            \sprintf(
                '
                    INSERT INTO %s (id, name, set, houses, sas, wins, losses, extra_data)
                    VALUES (:id, :name, :set, :houses, :sas, :wins, :losses, :extra_data)
                    ON CONFLICT (id) DO UPDATE SET
                        id = :id,
                        name = :name,
                        set = :set,
                        houses = :houses,
                        sas = :sas,
                        wins = :wins,
                        losses = :losses,
                        extra_data = :extra_data
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
        $stmt->bindValue(':extra_data', \json_encode($deck->extraData()));

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
            \json_decode($deck['extra_data'], true, 512, JSON_THROW_ON_ERROR),
        );
    }

    /** @return array<KeyforgeGame> */
    public function gamesByUser(UuidValueObject ...$ids): array
    {
        $ids = \array_map(static fn (UuidValueObject $id) => $id->value(), $ids);

        $result = $this->connection->createQueryBuilder()
            ->select('a.*')
            ->from(self::TABLE_GAMES, 'a')
            ->where('a.winner in (:ids)')
            ->orWhere('a.loser in (:ids)')
            ->setParameter('ids', $ids, Connection::PARAM_STR_ARRAY)
            ->orderBy('a.date', 'DESC')
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
            ->orWhere('a.loser_deck = :id')
            ->setParameter('id', $id->value())
            ->orderBy('a.date', 'DESC')
            ->execute()
            ->fetchAllAssociative();

        if (false === $result) {
            return [];
        }

        return \array_map(fn (array $game) => $this->mapGame($game), $result);
    }

    public function saveGame(KeyforgeGame $game): void
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
                self::TABLE_GAMES,
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

    private function mapGame(array $game): KeyforgeGame
    {
        $score = \json_decode($game['score'], true, 512, JSON_THROW_ON_ERROR);

        return new KeyforgeGame(
            UuidValueObject::from($game['id']),
            UuidValueObject::from($game['winner']),
            UuidValueObject::from($game['loser']),
            UuidValueObject::from($game['winner_deck']),
            UuidValueObject::from($game['loser_deck']),
            null === $game['first_turn'] ? null : UuidValueObject::from($game['first_turn']),
            KeyforgeGameScore::from($score['winner_score'], $score['loser_score']),
            new \DateTimeImmutable($game['date']),
        );
    }
}
