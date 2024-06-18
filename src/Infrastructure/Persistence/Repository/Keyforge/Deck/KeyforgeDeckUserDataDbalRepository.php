<?php declare(strict_types=1);

namespace AdnanMula\Cards\Infrastructure\Persistence\Repository\Keyforge\Deck;

use AdnanMula\Cards\Application\Service\Json;
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
                INSERT INTO %s (deck_id, owner, wins, losses, wins_vs_friends, losses_vs_friends, wins_vs_users, losses_vs_users, notes, user_tags)
                VALUES (:deck_id, :owner, :wins, :losses, :wins_vs_friends, :losses_vs_friends, :wins_vs_users, :losses_vs_users, :notes, :user_tags)
                ON CONFLICT (deck_id, owner) DO UPDATE SET
                    wins = :wins,
                    losses = :losses,
                    wins_vs_friends = :wins_vs_friends,
                    losses_vs_friends = :losses_vs_friends,
                    wins_vs_users = :wins_vs_users,
                    losses_vs_users = :losses_vs_users,
                    notes = :notes,
                    user_tags = :user_tags
                ',
                self::TABLE,
            ),
        );

        $stmt->bindValue(':deck_id', $data->deckId->value());
        $stmt->bindValue(':owner', $data->owner->value());
        $stmt->bindValue(':wins', $data->wins);
        $stmt->bindValue(':losses', $data->losses);
        $stmt->bindValue(':wins_vs_friends', $data->winsVsUsers);
        $stmt->bindValue(':losses_vs_friends', $data->lossesVsUsers);
        $stmt->bindValue(':wins_vs_users', $data->winsVsFriends);
        $stmt->bindValue(':losses_vs_users', $data->lossesVsFriends);
        $stmt->bindValue(':notes', $data->notes);
        $stmt->bindValue(':user_tags', Json::encode($data->tags));

        $stmt->executeStatement();
    }

    private function map(array $deck): KeyforgeDeckUserData
    {
        return KeyforgeDeckUserData::from(
            Uuid::from($deck['deck_id']),
            Uuid::from($deck['owner']),
            null,
            $deck['wins'],
            $deck['losses'],
            $deck['wins_vs_friends'],
            $deck['losses_vs_friends'],
            $deck['wins_vs_users'],
            $deck['losses_vs_users'],
            $deck['notes'],
            Json::decode($deck['user_tags']),
        );
    }
}
