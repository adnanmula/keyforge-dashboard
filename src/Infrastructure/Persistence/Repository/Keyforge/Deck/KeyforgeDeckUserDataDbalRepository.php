<?php declare(strict_types=1);

namespace AdnanMula\Cards\Infrastructure\Persistence\Repository\Keyforge\Deck;

use AdnanMula\Cards\Domain\Model\Keyforge\Deck\KeyforgeDeckUserDataRepository;
use AdnanMula\Cards\Domain\Model\Keyforge\Deck\ValueObject\KeyforgeDeckUserData;
use AdnanMula\Cards\Domain\Model\Shared\ValueObject\Uuid;
use AdnanMula\Cards\Infrastructure\Persistence\Repository\DbalRepository;
use AdnanMula\Criteria\Criteria;
use AdnanMula\Criteria\DbalCriteriaAdapter;

final class KeyforgeDeckUserDataDbalRepository extends DbalRepository implements KeyforgeDeckUserDataRepository
{
    private const TABLE = 'keyforge_decks_user_data';

    public function search(Criteria $criteria): array
    {
        $builder = $this->connection->createQueryBuilder();

        $query = $builder->select('a.*')->from(self::TABLE, 'a');

        (new DbalCriteriaAdapter($builder))->execute($criteria);

        $result = $query->executeQuery()->fetchAllAssociative();

        return \array_map(fn (array $row) => $this->map($row), $result);
    }

    public function searchOne(Criteria $criteria): ?KeyforgeDeckUserData
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

    public function count(Criteria $criteria): int
    {
        $builder = $this->connection->createQueryBuilder();
        $query = $builder->select('COUNT(a.id)')->from(self::TABLE, 'a');

        (new DbalCriteriaAdapter($builder))->execute($criteria);

        return $query->executeQuery()->fetchOne();
    }

    public function save(KeyforgeDeckUserData $data): void
    {
        $stmt = $this->connection->prepare(
            \sprintf(
                '
                INSERT INTO %s (deck_id, user_id, wins, losses, wins_vs_friends, losses_vs_friends, wins_vs_users, losses_vs_users)
                VALUES (:deck_id, :user_id, :wins, :losses, :wins_vs_friends, :losses_vs_friends, :wins_vs_users, :losses_vs_users)
                ON CONFLICT (deck_id, user_id) DO UPDATE SET
                    wins = :wins,
                    losses = :losses,
                    wins_vs_friends = :wins_vs_friends,
                    losses_vs_friends = :losses_vs_friends,
                    wins_vs_users = :wins_vs_users,
                    losses_vs_users = :losses_vs_users
                ',
                self::TABLE,
            ),
        );

        $stmt->bindValue(':deck_id', $data->deckId()->value());
        $stmt->bindValue(':user_id', $data->userId()->value());
        $stmt->bindValue(':wins', $data->wins());
        $stmt->bindValue(':losses', $data->losses());
        $stmt->bindValue(':wins_vs_friends', $data->winsVsFriends());
        $stmt->bindValue(':losses_vs_friends', $data->lossesVsFriends());
        $stmt->bindValue(':wins_vs_users', $data->winsVsUsers());
        $stmt->bindValue(':losses_vs_users', $data->lossesVsUsers());

        $stmt->executeStatement();
    }

    private function map(array $deck): KeyforgeDeckUserData
    {
        return KeyforgeDeckUserData::from(
            Uuid::from($deck['deck_id']),
            Uuid::from($deck['user_id']),
            $deck['wins'],
            $deck['losses'],
            $deck['wins_vs_friends'],
            $deck['losses_vs_friends'],
            $deck['wins_vs_users'],
            $deck['losses_vs_users'],
        );
    }
}
