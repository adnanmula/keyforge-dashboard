<?php declare(strict_types=1);

namespace AdnanMula\Cards\Infrastructure\Persistence\Repository\Keyforge\Game;

use AdnanMula\Cards\Application\Service\Json;
use AdnanMula\Cards\Domain\Model\Keyforge\Game\KeyforgeGame;
use AdnanMula\Cards\Domain\Model\Keyforge\Game\KeyforgeGameRepository;
use AdnanMula\Cards\Domain\Model\Keyforge\Game\ValueObject\KeyforgeCompetition;
use AdnanMula\Cards\Domain\Model\Keyforge\Game\ValueObject\KeyforgeGameScore;
use AdnanMula\Cards\Domain\Model\Shared\ValueObject\Uuid;
use AdnanMula\Cards\Infrastructure\Persistence\Repository\DbalRepository;
use AdnanMula\Criteria\Criteria;
use AdnanMula\Criteria\DbalCriteriaAdapter;

final class KeyforgeGameDbalRepository extends DbalRepository implements KeyforgeGameRepository
{
    private const TABLE = 'keyforge_games';

    public function search(Criteria $criteria): array
    {
        $builder = $this->connection->createQueryBuilder();

        $query = $builder->select('a.*')
            ->from(self::TABLE, 'a');

        (new DbalCriteriaAdapter($query))->execute($criteria);

        $result = $query->executeQuery()->fetchAllAssociative();

        if ([] === $result || false === $result) {
            return [];
        }

        return \array_map(fn (array $game) => $this->map($game), $result);
    }

    public function all(?int $offset = null, ?int $limit = null): array
    {
        $builder = $this->connection->createQueryBuilder();

        $query = $builder->select('a.*')
            ->from(self::TABLE, 'a')
            ->orderBy('a.date', 'DESC')
            ->addOrderBy('a.created_at', 'DESC');

        if (null !== $offset) {
            $query->setFirstResult($offset);
        }

        if (null !== $limit) {
            $query->setMaxResults($limit);
        }

        $result = $query->executeQuery()->fetchAllAssociative();

        if ([] === $result || false === $result) {
            return [];
        }

        return \array_map(fn (array $game) => $this->map($game), $result);
    }

    public function count(Criteria $criteria): int
    {
        $builder = $this->connection->createQueryBuilder();

        $query = $builder->select('count(a.*)')
            ->from(self::TABLE, 'a');

        (new DbalCriteriaAdapter($query))->execute($criteria);

        return $query->executeQuery()->fetchOne();
    }

    public function save(KeyforgeGame $game): void
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
        $stmt->bindValue(':score', Json::encode($game->score()));
        $stmt->bindValue(':date', $game->date()->format(\DateTimeInterface::ATOM));
        $stmt->bindValue(':created_at', $game->createdAt()->format(\DateTimeInterface::ATOM));
        $stmt->bindValue(':competition', $game->competition()->name);
        $stmt->bindValue(':notes', $game->notes());

        $stmt->executeStatement();
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
            $game['winner_chains'],
            $game['loser_chains'],
            null === $game['first_turn'] ? null : Uuid::from($game['first_turn']),
            KeyforgeGameScore::from($score['winner_score'], $score['loser_score']),
            new \DateTimeImmutable($game['date']),
            new \DateTimeImmutable($game['created_at']),
            KeyforgeCompetition::fromName($game['competition']),
            $game['notes'],
        );
    }
}