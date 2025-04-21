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
use Doctrine\DBAL\ParameterType;

final class KeyforgeGameDbalRepository extends DbalRepository implements KeyforgeGameRepository
{
    private const string TABLE = 'keyforge_games';

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

    public function searchOne(Criteria $criteria): ?KeyforgeGame
    {
        $criteria = new Criteria(
            $criteria->offset(),
            1,
            $criteria->sorting(),
            ...$criteria->filterGroups(),
        );

        $result = $this->search($criteria);

        return $result[0] ?? null;
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
                    INSERT INTO %s (id, winner, loser, winner_deck, loser_deck, first_turn, score, date, created_at, winner_chains, loser_chains, competition, notes, approved, created_by, log)
                    VALUES (:id, :winner, :loser, :winner_deck, :loser_deck, :first_turn, :score, :date, :created_at, :winner_chains, :loser_chains, :competition, :notes, :approved, :created_by, :log)
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
                        notes = :notes,
                        approved = :approved,
                        created_by = :created_by,
                        log = :log
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
        $stmt->bindValue(':approved', $game->approved(), ParameterType::BOOLEAN);
        $stmt->bindValue(':created_by', $game->createdBy()?->value());
        $stmt->bindValue(':log', null === $game->log() ? null : Json::encode($game->log()));

        $stmt->executeStatement();
    }

    public function remove(Uuid $id): void
    {
        $this->connection->createQueryBuilder()
            ->delete(self::TABLE, 'a')
            ->where('a.id = :id')
            ->setParameter('id', $id->value())
            ->executeStatement();
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
            Uuid::fromNullable($game['first_turn']),
            KeyforgeGameScore::from($score['winner_score'], $score['loser_score']),
            new \DateTimeImmutable($game['date']),
            new \DateTimeImmutable($game['created_at']),
            KeyforgeCompetition::fromName($game['competition']),
            $game['notes'],
            $game['approved'],
            Uuid::fromNullable($game['created_by']),
            Json::decodeNullable($game['log']),
        );
    }
}
