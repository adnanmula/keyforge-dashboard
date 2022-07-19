<?php declare(strict_types=1);

namespace AdnanMula\Cards\Infrastructure\Persistence\Repository\Keyforge;

use AdnanMula\Cards\Application\Service\Json;
use AdnanMula\Cards\Domain\Model\Keyforge\KeyforgeGame;
use AdnanMula\Cards\Domain\Model\Keyforge\KeyforgeGameRepository;
use AdnanMula\Cards\Domain\Model\Keyforge\ValueObject\KeyforgeGameScore;
use AdnanMula\Cards\Domain\Model\Shared\ValueObject\Uuid;
use AdnanMula\Cards\Infrastructure\Persistence\Repository\DbalRepository;
use Doctrine\DBAL\Connection;

final class KeyforgeGameDbalRepository extends DbalRepository implements KeyforgeGameRepository
{
    private const TABLE = 'keyforge_games';

    /** @return array<KeyforgeGame> */
    public function byUser(Uuid ...$ids): array
    {
        $ids = \array_map(static fn (Uuid $id) => $id->value(), $ids);

        $result = $this->connection->createQueryBuilder()
            ->select('a.*')
            ->from(self::TABLE, 'a')
            ->where('a.winner in (:ids)')
            ->orWhere('a.loser in (:ids)')
            ->setParameter('ids', $ids, Connection::PARAM_STR_ARRAY)
            ->orderBy('a.date', 'DESC')
            ->addOrderBy('a.created_at', 'DESC')
            ->execute()
            ->fetchAllAssociative();

        if ([] === $result || false === $result) {
            return [];
        }

        return \array_map(fn (array $game) => $this->map($game), $result);
    }

    /** @return array<KeyforgeGame> */
    public function byDeck(Uuid $id): array
    {
        $result = $this->connection->createQueryBuilder()
            ->select('a.*')
            ->from(self::TABLE, 'a')
            ->where('a.winner_deck = :id')
            ->orWhere('a.loser_deck = :id')
            ->setParameter('id', $id->value())
            ->orderBy('a.date', 'DESC')
            ->addOrderBy('a.created_at', 'DESC')
            ->execute()
            ->fetchAllAssociative();

        if ([] === $result || false === $result) {
            return [];
        }

        return \array_map(fn (array $game) => $this->map($game), $result);
    }

    public function byUsersAndDecks(array $users, array $decks): array
    {
        $userIds = \array_map(static fn (Uuid $id) => $id->value(), $users);
        $decksIds = \array_map(static fn (Uuid $id) => $id->value(), $decks);

        $builder = $this->connection->createQueryBuilder();

        $result = $builder->select('a.*')
            ->from(self::TABLE, 'a')
            ->where($builder->expr()->or('a.winner in (:user_ids)', 'a.loser in (:user_ids)'))
            ->andWhere($builder->expr()->and('a.winner_deck in (:decks_ids)', 'a.loser_deck in (:decks_ids)'))
            ->setParameter('user_ids', $userIds, Connection::PARAM_STR_ARRAY)
            ->setParameter('decks_ids', $decksIds, Connection::PARAM_STR_ARRAY)
            ->orderBy('a.date', 'DESC')
            ->addOrderBy('a.created_at', 'DESC')
            ->execute()
            ->fetchAllAssociative();

        if ([] === $result || false === $result) {
            return [];
        }

        return \array_map(fn (array $game) => $this->map($game), $result);
    }

    public function all(int $page, int $pageSize): array
    {
        $result = $this->connection->createQueryBuilder()
            ->select('a.*')
            ->from(self::TABLE, 'a')
            ->orderBy('a.date', 'DESC')
            ->addOrderBy('a.created_at', 'DESC')
            ->execute()
            ->fetchAllAssociative();

        if ([] === $result || false === $result) {
            return [];
        }

        return \array_map(fn (array $game) => $this->map($game), $result);
    }

    public function save(KeyforgeGame $game): void
    {
        $stmt = $this->connection->prepare(
            \sprintf(
                '
                    INSERT INTO %s (id, winner, loser, winner_deck, loser_deck, first_turn, score, date, created_at)
                    VALUES (:id, :winner, :loser, :winner_deck, :loser_deck, :first_turn, :score, :date, :created_at)
                    ON CONFLICT (id) DO UPDATE SET
                        id = :id,
                        winner = :winner,
                        loser = :loser,
                        winner_deck = :winner_deck,
                        loser_deck = :loser_deck,
                        first_turn = :first_turn,
                        score = :score,
                        date = :date,
                        created_at = :created_at
                    ',
                self::TABLE,
            ),
        );

        $stmt->bindValue(':id', $game->id()->value());
        $stmt->bindValue(':winner', $game->winner()->value());
        $stmt->bindValue(':loser', $game->loser()->value());
        $stmt->bindValue(':winner_deck', $game->winnerDeck()->value());
        $stmt->bindValue(':loser_deck', $game->loserDeck()->value());
        $stmt->bindValue(':first_turn', $game->firstTurn()?->value());
        $stmt->bindValue(':score', Json::encode($game->score()));
        $stmt->bindValue(':date', $game->date()->format(\DateTimeInterface::ATOM));
        $stmt->bindValue(':created_at', $game->createdAt()->format(\DateTimeInterface::ATOM));

        $stmt->execute();
    }

    private function map(array $game): KeyforgeGame
    {
        $score = Json::decode($game['score']);

        return new KeyforgeGame(
            Uuid::from($game['id']),
            Uuid::from($game['winner']),
            Uuid::from($game['loser']),
            Uuid::from($game['winner_deck']),
            Uuid::from($game['loser_deck']),
            null === $game['first_turn'] ? null : Uuid::from($game['first_turn']),
            KeyforgeGameScore::from($score['winner_score'], $score['loser_score']),
            new \DateTimeImmutable($game['date']),
            new \DateTimeImmutable($game['created_at']),
        );
    }
}
