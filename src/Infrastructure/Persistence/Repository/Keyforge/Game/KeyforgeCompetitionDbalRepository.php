<?php declare(strict_types=1);

namespace AdnanMula\Cards\Infrastructure\Persistence\Repository\Keyforge\Game;

use AdnanMula\Cards\Application\Service\Json;
use AdnanMula\Cards\Domain\Model\Keyforge\Game\KeyforgeCompetition;
use AdnanMula\Cards\Domain\Model\Keyforge\Game\KeyforgeCompetitionFixture;
use AdnanMula\Cards\Domain\Model\Keyforge\Game\KeyforgeCompetitionRepository;
use AdnanMula\Cards\Domain\Model\Shared\ValueObject\CompetitionVisibility;
use AdnanMula\Cards\Domain\Model\Shared\ValueObject\Uuid;
use AdnanMula\Cards\Infrastructure\Persistence\Repository\DbalRepository;
use AdnanMula\Criteria\Criteria;
use AdnanMula\Criteria\DbalCriteriaAdapter;
use AdnanMula\Tournament\Classification\Classification;
use AdnanMula\Tournament\Fixture\Fixtures;
use AdnanMula\Tournament\Fixture\FixtureType;
use AdnanMula\Tournament\TournamentType;
use AdnanMula\Tournament\User;
use Doctrine\DBAL\ArrayParameterType;
use Doctrine\DBAL\Connection;
use Symfony\Contracts\Translation\TranslatorInterface;

final class KeyforgeCompetitionDbalRepository extends DbalRepository implements KeyforgeCompetitionRepository
{
    private const string TABLE = 'keyforge_competitions';
    private const string TABLE_FIXTURES = 'keyforge_competition_fixtures';
    private const string TABLE_USERS = 'keyforge_users';

    public function __construct(Connection $connection, private TranslatorInterface $translator)
    {
        parent::__construct($connection);
    }

    public function search(Criteria $criteria): array
    {
        $builder = $this->connection->createQueryBuilder();

        $query = $builder->select('a.*')->from(self::TABLE, 'a');

        (new DbalCriteriaAdapter($builder))->execute($criteria);

        $result = $query->executeQuery()->fetchAllAssociative();

        $userIds = [];

        foreach ($result as $row) {
            $userIds = array_merge($userIds, Json::decode($row['admins']), Json::decode($row['players']));
        }

        $users = $this->searchUsers(...\array_unique($userIds));

        return \array_map(fn (array $row) => $this->map($row, $users), $result);
    }

    /** @return array<User> */
    private function searchUsers(string ...$ids): array
    {
        $users = $this->connection->createQueryBuilder()->select('a.*')
            ->from(self::TABLE_USERS, 'a')
            ->where('a.id IN (:ids)')
            ->setParameter('ids', $ids, ArrayParameterType::STRING)
            ->executeQuery()
            ->fetchAllAssociative();

        $indexedUsers = [];

        foreach ($users as $user) {
            $indexedUsers[$user['id']] = new User($user['id'], $user['name']);
        }

        return $indexedUsers;
    }

    public function searchOne(Criteria $criteria): ?KeyforgeCompetition
    {
        $result = $this->search(
            new Criteria($criteria->offset(), 1, $criteria->sorting(), ...$criteria->filterGroups())
        );

        return $result[0] ?? null;
    }

    public function count(Criteria $criteria): int
    {
        $builder = $this->connection->createQueryBuilder();
        $query = $builder->select('COUNT(a.id)')
            ->from(self::TABLE, 'a');

        (new DbalCriteriaAdapter($builder))->execute($criteria);

        return $query->executeQuery()->fetchOne();
    }

    public function save(KeyforgeCompetition $competition): void
    {
        $stmt = $this->connection->prepare(
            \sprintf(
                '
                    INSERT INTO %s (id, name, competition_type, fixtures_type, admins, players, description, visibility, created_at, started_at, finished_at, winner)
                    VALUES (:id, :name, :competition_type, :fixtures_type, :admins, :players, :description, :visibility, :created_at, :started_at, :finished_at, :winner)
                    ON CONFLICT (id) DO UPDATE SET
                        id = :id,
                        name = :name,
                        competition_type = :competition_type,
                        fixtures_type = :fixtures_type,
                        admins = :admins,
                        players = :players,
                        description = :description,
                        visibility = :visibility,
                        created_at = :created_at,
                        started_at = :started_at,
                        finished_at = :finished_at,
                        winner = :winner
                    ',
                self::TABLE,
            ),
        );

        $stmt->bindValue(':id', $competition->id->value());
        $stmt->bindValue(':name', $competition->name);
        $stmt->bindValue(':competition_type', $competition->type->name);
        $stmt->bindValue(':fixtures_type', $competition->fixtures->type->name);
        $stmt->bindValue(':admins', Json::encode($competition->adminIds()));
        $stmt->bindValue(':players', Json::encode($competition->playerIds()));
        $stmt->bindValue(':description', $competition->description);
        $stmt->bindValue(':visibility', $competition->visibility->name);
        $stmt->bindValue(':created_at', $competition->createdAt->format(\DateTimeInterface::ATOM));
        $stmt->bindValue(':started_at', $competition->startedAt?->format(\DateTimeInterface::ATOM));
        $stmt->bindValue(':finished_at', $competition->finishedAt?->format(\DateTimeInterface::ATOM));
        $stmt->bindValue(':winner', $competition->winner?->value());

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

        $userIds = [];

        foreach ($result as $row) {
            $userIds = array_merge($userIds, Json::decode($row['players']));
        }

        $users = $this->searchUsers(...\array_unique($userIds));

        return \array_map(fn (array $row) => $this->mapFixture($row, $users), $result);
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

        return $this->mapFixture(
            $result,
            $this->searchUsers(...Json::decode($result['players'])),
        );
    }

    public function saveFixture(KeyforgeCompetitionFixture $fixture): void
    {
        $stmt = $this->connection->prepare(
            \sprintf(
                '
                    INSERT INTO %s (id, competition_id, reference, players, fixture_type, position, created_at, played_at, winner, games)
                    VALUES (:id, :competition_id, :reference, :players, :fixture_type, :position, :created_at, :played_at, :winner, :games)
                    ON CONFLICT (id) DO UPDATE SET
                        competition_id = :competition_id,
                        reference = :reference,
                        players = :players,
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

        $stmt->bindValue(':id', $fixture->id->value());
        $stmt->bindValue(':competition_id', $fixture->competitionId->value());
        $stmt->bindValue(':reference', $fixture->reference);
        $stmt->bindValue(':players', Json::encode($fixture->playerIds()));
        $stmt->bindValue(':fixture_type', $fixture->type->name);
        $stmt->bindValue(':position', $fixture->position);
        $stmt->bindValue(':created_at', $fixture->createdAt->format(\DateTimeInterface::ATOM));
        $stmt->bindValue(':played_at', $fixture->playedAt?->format('Y-m-d'));
        $stmt->bindValue(':winner', $fixture->winner?->value());
        $stmt->bindValue(':games', Json::encode($fixture->games));

        $stmt->executeStatement();
    }

    private function map(array $row, array $users): KeyforgeCompetition
    {
        return new KeyforgeCompetition(
            Uuid::from($row['id']),
            $row['name'],
            $row['description'],
            TournamentType::from($row['competition_type']),
            \array_map(static fn (string $id): User => $users[$id] ?? new User($id, 'Unknown'), Json::decode($row['admins'])),
            \array_map(static fn (string $id): User => $users[$id] ?? new User($id, 'Unknown'), Json::decode($row['players'])),
            null === $row['created_at']
                ? null
                : new \DateTimeImmutable($row['created_at']),
            null === $row['started_at']
                ? null
                : new \DateTimeImmutable($row['started_at']),
            null === $row['finished_at']
                ? null
                : new \DateTimeImmutable($row['finished_at']),
            CompetitionVisibility::from($row['visibility']),
            Uuid::fromNullable($row['winner']),
            new Fixtures(FixtureType::from($row['fixtures_type']), $this->translator->trans('competition.round')),
            new Classification(null !== $row['finished_at']),
        );
    }

    private function mapFixture(array $row, array $users): KeyforgeCompetitionFixture
    {
        return new KeyforgeCompetitionFixture(
            Uuid::from($row['id']),
            Uuid::from($row['competition_id']),
            Uuid::fromNullable($row['winner']),
            \array_map(static fn (string $id) => Uuid::from($id), Json::decode($row['games'])),
            $row['reference'],
            \array_map(static fn (string $id): User => $users[$id] ?? new User($id, 'Unknown'), Json::decode($row['players'])),
            FixtureType::from($row['fixture_type']),
            $row['position'],
            new \DateTimeImmutable($row['created_at']),
            null === $row['played_at']
                ? null
                : new \DateTimeImmutable($row['played_at']),
        );
    }
}
