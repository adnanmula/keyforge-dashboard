<?php declare(strict_types=1);

namespace AdnanMula\Cards\Infrastructure\Persistence\Repository\Keyforge\User;

use AdnanMula\Cards\Domain\Model\Keyforge\User\KeyforgeUser;
use AdnanMula\Cards\Domain\Model\Keyforge\User\KeyforgeUserRepository;
use AdnanMula\Cards\Domain\Model\Shared\ValueObject\Uuid;
use AdnanMula\Cards\Infrastructure\Persistence\Repository\DbalRepository;
use AdnanMula\Criteria\Criteria;
use AdnanMula\Criteria\DbalCriteriaAdapter;
use Doctrine\DBAL\Result;

final class KeyforgeUserDbalRepository extends DbalRepository implements KeyforgeUserRepository
{
    private const TABLE = 'keyforge_users';

    private const FIELD_MAPPING = [
        'id' => 'a.id',
        'is_external' => 'a.owner',
    ];

    public function search(Criteria $criteria): array
    {
        $builder = $this->connection->createQueryBuilder();

        $query = $builder->select('a.*')->from(self::TABLE, 'a');

        (new DbalCriteriaAdapter($builder, self::FIELD_MAPPING))->execute($criteria);

        $result = $query->executeQuery()->fetchAllAssociative();

        return \array_map(fn (array $row) => $this->map($row), $result);
    }

    public function save(KeyforgeUser $user): void
    {
        $stmt = $this->connection->prepare(
            \sprintf(
                'INSERT INTO %s (id, name, owner)
                VALUES (:id, :name, :owner)
                ON CONFLICT (id) DO NOTHING',
                self::TABLE,
            ),
        );

        $stmt->bindValue(':id', $user->id()->value());
        $stmt->bindValue(':name', $user->name());
        $stmt->bindValue(':owner', $user->owner()?->value());

        $stmt->executeStatement();
    }

    public function winrate(Uuid $id): array
    {
        return $this->connection->executeQuery(
            "WITH games_played AS (
                SELECT u.id AS player_id, g.competition, COUNT(*) AS total_games
                FROM keyforge_games g
                JOIN keyforge_users u
                ON u.id = g.winner OR u.id = g.loser
                WHERE g.approved is true
                GROUP BY u.id, g.competition
            ),
            games_won AS (
                SELECT u.id AS player_id, g.competition, COUNT(*) AS total_wins
                FROM keyforge_games g
                JOIN keyforge_users u ON u.id = g.winner
                WHERE g.approved is true
                GROUP BY u.id, g.competition
            )
            
            SELECT 
                p.player_id,
                p.competition,
                p.total_games,
                COALESCE(w.total_wins, 0) AS total_wins,
                COALESCE(w.total_wins, 0)::float / p.total_games AS win_ratio
            FROM games_played p
            LEFT JOIN games_won w
            ON p.player_id = w.player_id AND p.competition = w.competition
            where p.player_id = '" . $id->value() . "'
            ORDER BY p.player_id, p.competition;"
        )->fetchAllAssociative();
    }

    public function bestDecks(Uuid $id): array
    {
        /** @var false|Result $result */
        $result = $this->connection->executeQuery(
            "WITH player_games AS (
            SELECT 
                g.id AS game_id,
                u.id AS player_id,
                g.winner,
                g.loser,
                g.winner_deck,
                g.loser_deck,
                CASE 
                    WHEN g.winner = u.id THEN g.winner_deck
                    ELSE g.loser_deck
                END AS played_deck,
                CASE 
                    WHEN g.winner = u.id THEN 'win'
                    ELSE 'loss'
                END AS result,
                d.houses,
                d.sas
            FROM keyforge_games g
            JOIN keyforge_users u ON u.id = g.winner OR u.id = g.loser
            JOIN keyforge_decks d ON d.id = g.winner_deck OR d.id = g.loser_deck
            JOIN keyforge_decks_ownership o ON o.deck_id = d.id
            WHERE u.id = '" . $id->value() . "' AND o.user_id = u.id
        ),
        
        deck_stats AS (
            SELECT 
                played_deck AS deck_id,
                COUNT(*) AS total_games,
                SUM(CASE WHEN result = 'win' THEN 1 ELSE 0 END) AS wins,
                AVG(CASE WHEN result = 'win' THEN 1 ELSE 0 END) AS win_ratio,
                MAX(sas) AS max_sas
            FROM player_games
            GROUP BY played_deck
        ),
        
        most_played_deck AS (
            SELECT deck_id, total_games
            FROM deck_stats
            ORDER BY total_games DESC
            LIMIT 1
        ),
        
        highest_sas_deck AS (
            SELECT deck_id, max_sas
            FROM deck_stats
            ORDER BY max_sas DESC
            LIMIT 1
        ),
        
        best_win_ratio_deck AS (
            SELECT deck_id, win_ratio
            FROM deck_stats
            WHERE total_games >= 10
            ORDER BY win_ratio DESC
            LIMIT 1
        ),
        
        house_stats AS (
            SELECT h.house, COUNT(*) AS total_games
            FROM player_games, jsonb_array_elements(houses) AS h(house)
            GROUP BY h.house
        ),
        
        most_played_house AS (
            SELECT house, total_games
            FROM house_stats
            ORDER BY total_games DESC
            LIMIT 1
        )
        
        SELECT 
            u.name AS player_name,
            (SELECT d.name FROM keyforge_decks d WHERE d.id = most_played_deck.deck_id) AS most_played_deck,
            most_played_deck.deck_id as most_played_deck_id,
            most_played_deck.total_games AS most_played_deck_games,
            (SELECT d.name FROM keyforge_decks d WHERE d.id = highest_sas_deck.deck_id) AS highest_sas_deck,
            highest_sas_deck.deck_id as highest_sas_deck_id,
            highest_sas_deck.max_sas AS highest_sas_deck_value,
            (SELECT d.name FROM keyforge_decks d WHERE d.id = best_win_ratio_deck.deck_id) AS best_win_ratio_deck,
            best_win_ratio_deck.deck_id as best_win_ratio_deck_id,
            best_win_ratio_deck.win_ratio AS best_win_ratio_deck_value,
            most_played_house.house AS most_played_house,
            most_played_house.total_games AS most_played_house_games
        FROM 
            keyforge_users u,
            most_played_deck,
            highest_sas_deck,
            best_win_ratio_deck,
            most_played_house
        WHERE 
            u.id = '" . $id->value() . "';"
        );

        if ($result === false) {
            return [];
        }

        $result = $result->fetchAssociative();

        if ($result === false) {
            return [];
        }

        return $result;
    }

    private function map(array $user): KeyforgeUser
    {
        return KeyforgeUser::create(
            Uuid::from($user['id']),
            $user['name'],
            Uuid::fromNullable($user['owner']),
        );
    }
}
