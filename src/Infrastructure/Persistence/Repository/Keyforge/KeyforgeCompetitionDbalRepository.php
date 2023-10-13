<?php declare(strict_types=1);

namespace AdnanMula\Cards\Infrastructure\Persistence\Repository\Keyforge;

use AdnanMula\Cards\Application\Service\Json;
use AdnanMula\Cards\Domain\Model\Keyforge\KeyforgeCompetition;
use AdnanMula\Cards\Domain\Model\Keyforge\KeyforgeCompetitionFixture;
use AdnanMula\Cards\Domain\Model\Keyforge\KeyforgeCompetitionRepository;
use AdnanMula\Cards\Domain\Model\Shared\ValueObject\CompetitionFixtureType;
use AdnanMula\Cards\Domain\Model\Shared\ValueObject\CompetitionType;
use AdnanMula\Cards\Domain\Model\Shared\ValueObject\Uuid;
use AdnanMula\Cards\Infrastructure\Persistence\Repository\DbalRepository;
use AdnanMula\Criteria\Criteria;
use AdnanMula\Criteria\DbalCriteriaAdapter;

final class KeyforgeCompetitionDbalRepository extends DbalRepository implements KeyforgeCompetitionRepository
{
    private const TABLE = 'keyforge_competitions';
    private const TABLE_FIXTURES = 'keyforge_competition_fixtures';

    public function search(Criteria $criteria): array
    {
        $builder = $this->connection->createQueryBuilder();

        $query = $builder->select('a.id, a.reference, a.name, a.competition_type, a.users, a.description, a.created_at, a.started_at, a.finished_at, a.winner')
            ->from(self::TABLE, 'a');

        (new DbalCriteriaAdapter($builder))->execute($criteria);

        $result = $query->executeQuery()->fetchAllAssociative();

        return \array_map(fn (array $row) => $this->map($row), $result);
    }

    public function count(Criteria $criteria): int
    {
        $builder = $this->connection->createQueryBuilder();
        $query = $builder->select('COUNT(a.id)')
            ->from(self::TABLE, 'a');

        (new DbalCriteriaAdapter($builder))->execute($criteria);

        return $query->executeQuery()->fetchOne();
    }

    public function byId(Uuid $id): ?KeyforgeCompetition
    {
        $result = $this->connection->createQueryBuilder()
            ->select('a.id, a.reference, a.name, a.competition_type, a.users, a.description, a.created_at, a.started_at, a.finished_at, a.winner')
            ->from(self::TABLE, 'a')
            ->where('a.id = :id')
            ->setParameter('id', $id->value())
            ->setMaxResults(1)
            ->executeQuery()
            ->fetchAssociative();

        if ([] === $result || false === $result) {
            return null;
        }

        return $this->map($result);
    }

    public function byReference(string $reference): ?KeyforgeCompetition
    {
        $result = $this->connection->createQueryBuilder()
            ->select('a.id, a.reference, a.name, a.competition_type, a.users, a.description, a.created_at, a.started_at, a.finished_at, a.winner')
            ->from(self::TABLE, 'a')
            ->where('a.reference = :ref')
            ->setParameter('ref', $reference)
            ->setMaxResults(1)
            ->executeQuery()
            ->fetchAssociative();

        if ([] === $result || false === $result) {
            return null;
        }

        return $this->map($result);
    }


    public function save(KeyforgeCompetition $competition): void
    {
        $stmt = $this->connection->prepare(
            \sprintf(
                '
                    INSERT INTO %s (id, reference, name, competition_type, users, description, created_at, started_at, finished_at, winner)
                    VALUES (:id, :reference, :name, :competition_type, :users, :description, :created_at, :started_at, :finished_at, :winner)
                    ON CONFLICT (id) DO UPDATE SET
                        id = :id,
                        reference = :reference,
                        name = :name,
                        competition_type = :competition_type,
                        users = :users,
                        description = :description,
                        created_at = :created_at,
                        started_at = :started_at,
                        finished_at = :finished_at,
                        winner = :winner
                    ',
                self::TABLE,
            ),
        );

        $stmt->bindValue(':id', $competition->id()->value());
        $stmt->bindValue(':reference', $competition->reference());
        $stmt->bindValue(':name', $competition->name());
        $stmt->bindValue(':competition_type', $competition->type()->name);
        $stmt->bindValue(':users', Json::encode($competition->users()));
        $stmt->bindValue(':description', $competition->description());
        $stmt->bindValue(':created_at', $competition->createdAt()->format(\DateTimeInterface::ATOM));
        $stmt->bindValue(':started_at', $competition->startedAt()?->format(\DateTimeInterface::ATOM));
        $stmt->bindValue(':finished_at', $competition->finishedAt()?->format(\DateTimeInterface::ATOM));
        $stmt->bindValue(':winner', $competition->winner()?->value());

        $stmt->executeStatement();
    }

    /** @return array<KeyforgeCompetitionFixture> */
    public function fixtures(Uuid $competitionId): array
    {
        $result = $this->connection->createQueryBuilder()
            ->select('a.*')
            ->from(self::TABLE_FIXTURES, 'a')
            ->where('a.competition_id = :id')
            ->setParameter('id', $competitionId->value())
            ->executeQuery()
            ->fetchAllAssociative();

        if ([] === $result || false === $result) {
            return [];
        }

        return \array_map(fn (array $row) => $this->mapFixture($row), $result);
    }

    public function fixtureById(Uuid $id): ?KeyforgeCompetitionFixture
    {
        $result = $this->connection->createQueryBuilder()
            ->select('a.*')
            ->from(self::TABLE_FIXTURES, 'a')
            ->where('a.id = :id')
            ->setParameter('id', $id->value())
            ->executeQuery()
            ->fetchAssociative();

        if ([] === $result || false === $result) {
            return null;
        }

        return $this->mapFixture($result);
    }

    public function saveFixture(KeyforgeCompetitionFixture $fixture): void
    {
        $stmt = $this->connection->prepare(
            \sprintf(
                '
                    INSERT INTO %s (id, competition_id, reference, users, fixture_type, position, created_at, played_at, winner, games)
                    VALUES (:id, :competition_id, :reference, :users, :fixture_type, :position, :created_at, :played_at, :winner, :games)
                    ON CONFLICT (id) DO UPDATE SET
                        competition_id = :competition_id,
                        reference = :reference,
                        users = :users,
                        fixture_type = :fixture_type,
                        position = :position,
                        created_at = :created_at,
                        played_at = :played_at,
                        winner = :winner,
                        games = :games
                    ',
                self::TABLE_FIXTURES,
            ),
        );

        $stmt->bindValue(':id', $fixture->id()->value());
        $stmt->bindValue(':competition_id', $fixture->competitionId()->value());
        $stmt->bindValue(':reference', $fixture->reference());
        $stmt->bindValue(':users', Json::encode($fixture->users()));
        $stmt->bindValue(':fixture_type', $fixture->type()->name);
        $stmt->bindValue(':position', $fixture->position());
        $stmt->bindValue(':created_at', $fixture->createdAt()->format(\DateTimeInterface::ATOM));
        $stmt->bindValue(':played_at', $fixture->playedAt()?->format('Y-m-d'));
        $stmt->bindValue(':winner', $fixture->winner()?->value());
        $stmt->bindValue(':games', Json::encode($fixture->games()));

        $stmt->executeStatement();
    }

    private function map(array $row): KeyforgeCompetition
    {
        return new KeyforgeCompetition(
            Uuid::from($row['id']),
            $row['reference'],
            $row['name'],
            CompetitionType::from($row['competition_type']),
            \array_map(static fn (string $id): Uuid => Uuid::from($id), Json::decode($row['users'])),
            $row['description'],
            null === $row['created_at']
                ? null
                : new \DateTimeImmutable($row['created_at']),
            null === $row['started_at']
                ? null
                : new \DateTimeImmutable($row['started_at']),
            null === $row['finished_at']
                ? null
                : new \DateTimeImmutable($row['finished_at']),
            null === $row['winner']
                ? null
                : Uuid::from($row['winner']),
        );
    }

    private function mapFixture(array $row): KeyforgeCompetitionFixture
    {
        return new KeyforgeCompetitionFixture(
            Uuid::from($row['id']),
            Uuid::from($row['competition_id']),
            $row['reference'],
            \array_map(static fn (string $id): Uuid => Uuid::from($id), Json::decode($row['users'])),
            CompetitionFixtureType::from($row['fixture_type']),
            $row['position'],
            new \DateTimeImmutable($row['created_at']),
            null === $row['played_at']
                ? null
                : new \DateTimeImmutable($row['played_at']),
            null === $row['winner']
                ? null
                : Uuid::from($row['winner']),
            \array_map(static fn (string $id) => Uuid::from($id), Json::decode($row['games'])),
        );
    }
}
