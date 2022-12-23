<?php declare(strict_types=1);

namespace AdnanMula\Cards\Infrastructure\Persistence\Repository\Keyforge;

use AdnanMula\Cards\Application\Service\Json;
use AdnanMula\Cards\Domain\Model\Keyforge\KeyforgeCompetition;
use AdnanMula\Cards\Domain\Model\Keyforge\KeyforgeCompetitionRepository;
use AdnanMula\Cards\Domain\Model\Shared\ValueObject\CompetitionType;
use AdnanMula\Cards\Domain\Model\Shared\ValueObject\Uuid;
use AdnanMula\Cards\Infrastructure\Criteria\Criteria;
use AdnanMula\Cards\Infrastructure\Criteria\DbalCriteriaAdapter;
use AdnanMula\Cards\Infrastructure\Persistence\Repository\DbalRepository;
use Doctrine\DBAL\Connection;

final class KeyforgeCompetitionDbalRepository extends DbalRepository implements KeyforgeCompetitionRepository
{
    private const TABLE = 'keyforge_competitions';

    public function search(Criteria $criteria): array
    {
        $builder = $this->connection->createQueryBuilder();

        $query = $builder->select('a.id, a.reference, a.name, a.competition_type, a.users, a.description, a.created_at, a.started_at, a.finished_at, a.winner')
            ->from(self::TABLE, 'a');

        (new DbalCriteriaAdapter($builder))->execute($criteria);

        $result = $query->execute()->fetchAllAssociative();

        return \array_map(fn (array $row) => $this->map($row), $result);
    }

    public function byId(Uuid $id): ?KeyforgeCompetition
    {
        $result = $this->connection->createQueryBuilder()
            ->select('a.id, a.reference, a.name, a.competition_type, a.users, a.description, a.created_at, a.started_at, a.finished_at, a.winner')
            ->from(self::TABLE, 'a')
            ->where('a.id = :id')
            ->setParameter('id', $id->value())
            ->setMaxResults(1)
            ->execute()
            ->fetchAssociative();

        if ([] === $result || false === $result) {
            return null;
        }

        return $this->map($result);
    }

    public function byIds(Uuid ...$ids): array
    {
        $result = $this->connection->createQueryBuilder()
            ->select('a.id, a.reference, a.name, a.competition_type, a.users, a.description, a.created_at, a.started_at, a.finished_at, a.winner')
            ->from(self::TABLE, 'a')
            ->where('a.id in (:ids)')
            ->setParameter('ids', \array_map(static fn (Uuid $id) => $id->value(), $ids), Connection::PARAM_STR_ARRAY)
            ->execute()
            ->fetchAllAssociative();

        if ([] === $result || false === $result) {
            return [];
        }

        return \array_map(fn (array $deck) => $this->map($deck), $result);
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
        $stmt->bindValue(':started_at', $competition->startAt()?->format(\DateTimeInterface::ATOM));
        $stmt->bindValue(':finished_at', $competition->finishedAt()?->format(\DateTimeInterface::ATOM));
        $stmt->bindValue(':winner', $competition->winner()?->value());

        $stmt->execute();
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
}
