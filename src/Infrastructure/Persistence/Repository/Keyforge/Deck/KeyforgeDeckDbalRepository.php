<?php declare(strict_types=1);

namespace AdnanMula\Cards\Infrastructure\Persistence\Repository\Keyforge\Deck;

use AdnanMula\Cards\Application\Service\Json;
use AdnanMula\Cards\Domain\Model\Keyforge\Deck\KeyforgeDeck;
use AdnanMula\Cards\Domain\Model\Keyforge\Deck\KeyforgeDeckRepository;
use AdnanMula\Cards\Domain\Model\Keyforge\Deck\ValueObject\KeyforgeCards;
use AdnanMula\Cards\Domain\Model\Keyforge\Deck\ValueObject\KeyforgeDeckHouses;
use AdnanMula\Cards\Domain\Model\Keyforge\Deck\ValueObject\KeyforgeDeckStats;
use AdnanMula\Cards\Domain\Model\Keyforge\Deck\ValueObject\KeyforgeDeckType;
use AdnanMula\Cards\Domain\Model\Keyforge\Deck\ValueObject\KeyforgeDeckUserData;
use AdnanMula\Cards\Domain\Model\Keyforge\Deck\ValueObject\KeyforgeHouse;
use AdnanMula\Cards\Domain\Model\Keyforge\Deck\ValueObject\KeyforgeSet;
use AdnanMula\Cards\Domain\Model\Shared\ValueObject\Uuid;
use AdnanMula\Cards\Infrastructure\Persistence\Repository\DbalRepository;
use AdnanMula\Criteria\Criteria;
use AdnanMula\Criteria\DbalCriteriaAdapter;
use AdnanMula\Criteria\FilterField\FieldMapping;

final class KeyforgeDeckDbalRepository extends DbalRepository implements KeyforgeDeckRepository
{
    private const string TABLE = 'keyforge_decks';
    private const string TABLE_OWNERSHIP = 'keyforge_decks_ownership';
    private const string TABLE_USER_DATA = 'keyforge_decks_user_data';

    private const array FIELD_MAPPING = [
        'id' => 'a.id',
        'owner' => 'b.user_id',
        'user_stat' => 'c.user_id',
        'wins' => 'wins',
        'losses' => 'losses',
        'user_tags' => 'b.user_tags',
    ];

    public function search(Criteria $criteria, bool $isMyDecks = false): array
    {
        $builder = $this->connection->createQueryBuilder();

        $condition = 'a.id = c.deck_id';

        if ($isMyDecks) {
            $condition = 'a.id = c.deck_id and b.user_id = c.user_id';
        }

        $query = $builder->select('a.*')
            ->addSelect("string_agg(b.user_id::varchar, ',') as owners")
            ->addSelect("string_agg(c.user_id::varchar, ',') as stats_from_users")
            ->addSelect('COALESCE(SUM(c.wins), 0) as wins, COALESCE(SUM(c.losses), 0) as losses')
            ->addSelect('COALESCE(SUM(c.wins_vs_friends), 0) as wins_vs_friends, COALESCE(SUM(c.losses_vs_friends), 0) as losses_vs_friends')
            ->addSelect('COALESCE(SUM(c.wins_vs_users), 0) as wins_vs_users, COALESCE(SUM(c.losses_vs_users), 0) as losses_vs_users')
            ->from(self::TABLE, 'a')
            ->leftJoin('a', self::TABLE_OWNERSHIP, 'b', 'a.id = b.deck_id')
            ->leftJoin('a', self::TABLE_USER_DATA, 'c', $condition)
            ->groupBy('a.id');

        new DbalCriteriaAdapter($builder, new FieldMapping(self::FIELD_MAPPING))->execute($criteria);

        $result = $query->executeQuery()->fetchAllAssociative();

        return \array_map(fn (array $row) => $this->map($row), $result);
    }

    public function searchOne(Criteria $criteria): ?KeyforgeDeck
    {
        $result = $this->search(
            new Criteria(
                $criteria->filters(),
                $criteria->offset(),
                1,
                $criteria->sorting(),
            ),
        );

        return $result[0] ?? null;
    }

    public function count(Criteria $criteria): int
    {
        $builder = $this->connection->createQueryBuilder();
        $query = $builder->select('COUNT(a.id)')
            ->from(self::TABLE, 'a')
            ->leftJoin('a', self::TABLE_OWNERSHIP, 'b', 'a.id = b.deck_id');

        new DbalCriteriaAdapter($builder, new FieldMapping(self::FIELD_MAPPING))->execute($criteria);

        $result = $query->executeQuery()->fetchOne();

        if (false === $result) {
            return 0;
        }

        return  $result;
    }

    public function save(KeyforgeDeck ...$decks): void
    {
        if ([] === $decks) {
            return;
        }

        foreach (array_chunk($decks, 500) as $chunk) {
            $this->saveChunk(...$chunk);
        }
    }

    private function saveChunk(KeyforgeDeck ...$decks): void
    {
        $values = [];
        $params = [];

        foreach ($decks as $i => $deck) {
            $values[] = sprintf(
                '(
                    :id_%1$d, :name_%1$d, :set_%1$d, :houses_%1$d, :dok_id_%1$d, :sas_%1$d,
                    :amber_control_%1$d, :artifact_control_%1$d, :expected_amber_%1$d, :creature_control_%1$d, :efficiency_%1$d, :recursion_%1$d, :disruption_%1$d, :effective_power_%1$d, :creature_protection_%1$d,
                    :other_%1$d, :raw_amber_%1$d, :total_power_%1$d, :total_armor_%1$d, :efficiency_bonus_%1$d, :creature_count_%1$d, :action_count_%1$d, :artifact_count_%1$d, :upgrade_count_%1$d, :card_draw_count_%1$d,
                    :card_archive_count_%1$d, :key_cheat_count_%1$d, :board_clear_count_%1$d, :board_clear_cards_%1$d, :scaling_amber_control_count_%1$d, :scaling_amber_control_cards_%1$d, :synergy_rating_%1$d, :anti_synergy_rating_%1$d,
                    :aerc_score_%1$d, :aerc_version_%1$d, :sas_version_%1$d, :sas_percentile_%1$d, :previous_sas_rating_%1$d, :previous_major_sas_rating_%1$d, :last_sas_update_%1$d, :cards_%1$d, :tags_%1$d, :deck_type_%1$d
                )',
                $i,
            );

            $params['id_'.$i] = $deck->id()->value();
            $params['name_'.$i] = $deck->name();
            $params['set_'.$i] = $deck->set()->name;
            $params['houses_'.$i] = Json::encode($deck->houses()->value());
            $params['dok_id_'.$i] = $deck->dokId();
            $params['amber_control_'.$i] = $deck->stats()->amberControl;
            $params['artifact_control_'.$i] = $deck->stats()->artifactControl;
            $params['expected_amber_'.$i] = $deck->stats()->expectedAmber;
            $params['creature_control_'.$i] = $deck->stats()->creatureControl;
            $params['efficiency_'.$i] = $deck->stats()->efficiency;
            $params['recursion_'.$i] = $deck->stats()->recursion;
            $params['disruption_'.$i] = $deck->stats()->disruption;
            $params['effective_power_'.$i] = $deck->stats()->effectivePower;
            $params['creature_protection_'.$i] = $deck->stats()->creatureProtection;
            $params['other_'.$i] = $deck->stats()->other;
            $params['raw_amber_'.$i] = $deck->stats()->rawAmber;
            $params['total_power_'.$i] = $deck->stats()->totalPower;
            $params['total_armor_'.$i] = $deck->stats()->totalArmor;
            $params['efficiency_bonus_'.$i] = $deck->stats()->efficiencyBonus;
            $params['creature_count_'.$i] = $deck->stats()->creatureCount;
            $params['action_count_'.$i] = $deck->stats()->actionCount;
            $params['artifact_count_'.$i] = $deck->stats()->artifactCount;
            $params['upgrade_count_'.$i] = $deck->stats()->upgradeCount;
            $params['card_draw_count_'.$i] = $deck->stats()->cardDrawCount;
            $params['card_archive_count_'.$i] = $deck->stats()->cardArchiveCount;
            $params['key_cheat_count_'.$i] = $deck->stats()->keyCheatCount;
            $params['board_clear_count_'.$i] = $deck->stats()->boardClearCount;
            $params['board_clear_cards_'.$i] = Json::encode($deck->stats()->boardClearCards);
            $params['scaling_amber_control_count_'.$i] = $deck->stats()->scalingAmberControlCount;
            $params['scaling_amber_control_cards_'.$i] = Json::encode($deck->stats()->scalingAmberControlCards);
            $params['synergy_rating_'.$i] = $deck->stats()->synergyRating;
            $params['anti_synergy_rating_'.$i] = $deck->stats()->antiSynergyRating;
            $params['sas_'.$i] = $deck->stats()->sas;
            $params['previous_sas_rating_'.$i] = $deck->stats()->previousSasRating;
            $params['previous_major_sas_rating_'.$i] = $deck->stats()->previousMajorSasRating;
            $params['sas_percentile_'.$i] = $deck->stats()->sasPercentile;
            $params['aerc_score_'.$i] = $deck->stats()->aercScore;
            $params['aerc_version_'.$i] = $deck->stats()->aercVersion;
            $params['sas_version_'.$i] = $deck->stats()->sasVersion;
            $params['last_sas_update_'.$i] = $deck->stats()->lastSasUpdate?->format(\DateTimeInterface::ATOM);
            $params['cards_'.$i] = Json::encode($deck->cards()->jsonSerialize());
            $params['tags_'.$i] = Json::encode($deck->tags());
            $params['deck_type_'.$i] = $deck->type()->value;
        }

        $sql = sprintf(
            '
                INSERT INTO %s (
                    id, name, set, houses, dok_id, sas,
                    amber_control, artifact_control, expected_amber, creature_control, efficiency, recursion, disruption, effective_power, creature_protection, other,
                    raw_amber, total_power, total_armor, efficiency_bonus, creature_count, action_count, artifact_count, upgrade_count, card_draw_count, card_archive_count, key_cheat_count,
                    board_clear_count, board_clear_cards, scaling_amber_control_count, scaling_amber_control_cards, synergy_rating, anti_synergy_rating,
                    aerc_score, aerc_version, sas_version, sas_percentile, previous_sas_rating, previous_major_sas_rating,
                    last_sas_update, cards, tags, deck_type
                ) VALUES %s
                ON CONFLICT (id) DO UPDATE SET
                    sas = EXCLUDED.sas,
                    amber_control = EXCLUDED.amber_control,
                    artifact_control = EXCLUDED.artifact_control,
                    expected_amber = EXCLUDED.expected_amber,
                    creature_control = EXCLUDED.creature_control,
                    efficiency = EXCLUDED.efficiency,
                    recursion = EXCLUDED.recursion,
                    disruption = EXCLUDED.disruption,
                    effective_power = EXCLUDED.effective_power,
                    creature_protection = EXCLUDED.creature_protection,
                    other = EXCLUDED.other,
                    raw_amber = EXCLUDED.raw_amber,
                    total_power = EXCLUDED.total_power,
                    total_armor = EXCLUDED.total_armor,
                    efficiency_bonus = EXCLUDED.efficiency_bonus,
                    creature_count = EXCLUDED.creature_count,
                    action_count = EXCLUDED.action_count,
                    artifact_count = EXCLUDED.artifact_count,
                    upgrade_count = EXCLUDED.upgrade_count,
                    card_draw_count = EXCLUDED.card_draw_count,
                    card_archive_count = EXCLUDED.card_archive_count,
                    key_cheat_count = EXCLUDED.key_cheat_count,
                    board_clear_count = EXCLUDED.board_clear_count,
                    board_clear_cards = EXCLUDED.board_clear_cards,
                    scaling_amber_control_count = EXCLUDED.scaling_amber_control_count,
                    scaling_amber_control_cards = EXCLUDED.scaling_amber_control_cards,
                    synergy_rating = EXCLUDED.synergy_rating,
                    anti_synergy_rating = EXCLUDED.anti_synergy_rating,
                    aerc_score = EXCLUDED.aerc_score,
                    aerc_version = EXCLUDED.aerc_version,
                    sas_version = EXCLUDED.sas_version,
                    sas_percentile = EXCLUDED.sas_percentile,
                    previous_sas_rating = EXCLUDED.previous_sas_rating,
                    previous_major_sas_rating = EXCLUDED.previous_major_sas_rating,
                    last_sas_update = EXCLUDED.last_sas_update,
                    cards = EXCLUDED.cards,
                    tags = EXCLUDED.tags
            ',
            self::TABLE,
            implode(', ', $values),
        );

        $this->connection->executeStatement($sql, $params);
    }

    public function addOwner(Uuid $userId, Uuid ...$deckIds): void
    {
        if ([] === $deckIds) {
            return;
        }

        $values = [];
        $params = [];

        foreach (array_chunk($deckIds, 500) as $chunk) {
            foreach ($chunk as $i => $deckId) {

                $values[] = sprintf(
                    '(:deck_id_%1$d, :user_id_%1$d, :notes_%1$d, :user_tags_%1$d)',
                    $i,
                );

                $params['deck_id_'.$i] = $deckId->value();
                $params['user_id_'.$i] = $userId->value();
                $params['notes_'.$i] = '';
                $params['user_tags_'.$i] = Json::encode([]);
            }

            $sql = sprintf(
                '
                INSERT INTO %s (deck_id, user_id, notes, user_tags)
                VALUES %s
                ON CONFLICT (deck_id, user_id) DO NOTHING
            ',
                self::TABLE_OWNERSHIP,
                implode(', ', $values),
            );

            $this->connection->executeStatement($sql, $params);
        }
    }

    public function removeOwner(Uuid $deckId, Uuid $userId): void
    {
        $stmt = $this->connection->prepare(
            \sprintf(
                'DELETE FROM %s a WHERE a.deck_id = :deck_id and a.user_id = :user_id',
                self::TABLE_OWNERSHIP,
            ),
        );

        $stmt->bindValue(':deck_id', $deckId->value());
        $stmt->bindValue(':user_id', $userId->value());
        $stmt->executeStatement();
    }

    /** @return array<array{deck_id: string, user_id: string, notes: string}> */
    public function ownersOf(Uuid $deckId): array
    {
        return $this->connection->createQueryBuilder()
            ->select('a.*')
            ->from(self::TABLE_OWNERSHIP, 'a')
            ->where('a.deck_id = :deck_id')
            ->setParameter('deck_id', $deckId->value())
            ->executeQuery()
            ->fetchAllAssociative();
    }

    /** @return array<array{deck_id: string, user_id: string, notes: string, user_tags: string}> */
    public function ownedBy(Uuid $userId): array
    {
        return $this->connection->createQueryBuilder()
            ->select('a.*')
            ->from(self::TABLE_OWNERSHIP, 'a')
            ->where('a.user_id = :user_id')
            ->setParameter('user_id', $userId->value())
            ->executeQuery()
            ->fetchAllAssociative();
    }

    public function ownedInfo(Uuid $userId, Uuid $deckId): ?array
    {
        $result = $this->connection->createQueryBuilder()
            ->select('a.*')
            ->from(self::TABLE_OWNERSHIP, 'a')
            ->where('a.user_id = :user_id')
            ->andWhere('a.deck_id = :deck_id')
            ->setParameter('user_id', $userId->value())
            ->setParameter('deck_id', $deckId->value())
            ->executeQuery()
            ->fetchAssociative();

        if (false === $result) {
            return null;
        }

        return $result;
    }

    public function updateUserTags(Uuid $userId, Uuid $deckId, string ...$tags): void
    {
        $this->connection->createQueryBuilder()
            ->update(self::TABLE_OWNERSHIP)
            ->set('user_tags', ':user_tags')
            ->where('deck_id = :deck_id')
            ->andWhere('user_id = :user_id')
            ->setParameter('deck_id', $deckId->value())
            ->setParameter('user_id', $userId->value())
            ->setParameter('user_tags', Json::encode($tags))
            ->executeStatement();
    }

    public function updateNotes(Uuid $userId, Uuid $deckId, string $notes): void
    {
        $this->connection->createQueryBuilder()
            ->update(self::TABLE_OWNERSHIP)
            ->set('notes', ':notes')
            ->where('deck_id = :deck_id')
            ->andWhere('user_id = :user_id')
            ->setParameter('deck_id', $deckId->value())
            ->setParameter('user_id', $userId->value())
            ->setParameter('notes', $notes)
            ->executeStatement();
    }

    public function bellCurve(?KeyforgeDeckType $deckType): array
    {
        $stats = ['sas', 'expected_amber', 'amber_control', 'creature_control', 'artifact_control'];
        $result = [];

        foreach ($stats as $stat) {
            $query = $this->connection->createQueryBuilder()
                ->select('ROUND(a.' . $stat . ', 0) as stat, count(a.*) as count')
                ->from(self::TABLE, 'a')
                ->innerJoin('a', self::TABLE_OWNERSHIP, 'b', 'a.id = b.deck_id')
                ->where('a.sas > 30')
                ->groupBy('ROUND(a.' . $stat . ', 0)')
                ->orderBy('ROUND(a.' . $stat . ', 0)', 'asc');

            if (null !== $deckType) {
                $query->andWhere('a.deck_type = :deckType')
                    ->setParameter('deckType', $deckType->value);
            }

            $resultStats = $query->executeQuery()->fetchAllAssociative();

            foreach ($resultStats as $resultStat) {
                $result[$stat][$resultStat['stat']] = $resultStat['count'];
            }
        }

        return $result;
    }

    public function homeCounts(): array
    {
        $housesResult = $this->connection->executeQuery(
            'SELECT house AS house, COUNT(*) AS count
            FROM keyforge_decks a, jsonb_array_elements_text(houses) AS house
            where a.deck_type = \'STANDARD\'
            GROUP BY house;'
        )->fetchAllAssociative();

        $houses = [];
        foreach ($housesResult as $r) {
            $houses[$r['house']] = $r['count'];
        }

        $setsResult = $this->connection->executeQuery(
            'SELECT set, COUNT(*) AS count
            FROM keyforge_decks a
            left join keyforge_decks_ownership b on a.id = b.deck_id
            where b.deck_id is not null and a.sas > 30 and a.deck_type = \'STANDARD\'
            GROUP BY set;'
        )->fetchAllAssociative();

        $sets = [];
        foreach ($setsResult as $r) {
            $sets[$r['set']] = $r['count'];
        }

        $wrBySetResult = $this->connection->executeQuery(
            'SELECT 
                    d.set,
                    COUNT(DISTINCT g_winner.id) AS wins,
                    COUNT(DISTINCT g_loser.id) AS losses,
                    ROUND(COUNT(DISTINCT g_winner.id)::NUMERIC /
                    NULLIF((COUNT(DISTINCT g_winner.id) + COUNT(DISTINCT g_loser.id)), 0) * 100, 2) AS winrate
                FROM keyforge_decks d
                LEFT JOIN keyforge_decks_ownership o on d.id = o.deck_id
                LEFT JOIN keyforge_games g_winner ON d.id = g_winner.winner_deck and g_winner.approved is true
                LEFT JOIN keyforge_games g_loser ON d.id = g_loser.loser_deck and g_loser.approved is true
                WHERE o.deck_id is not null and d.sas > 30 and d.deck_type = \'STANDARD\'
                GROUP BY d.set
                ORDER BY winrate DESC;'
        )->fetchAllAssociative();

        $wrBySet = [];
        foreach ($wrBySetResult as $r) {
            $wrBySet[$r['set']] = $r;
        }

        $wrBySasResult = $this->connection->executeQuery(
            "WITH sas_wins AS (
                SELECT d.sas, COUNT(g.id) AS wins
                FROM keyforge_decks d
                LEFT JOIN keyforge_decks_ownership o on d.id = o.deck_id
                LEFT JOIN keyforge_games g ON d.id = g.winner_deck and g.approved is true
                where d.sas > 29 and g.competition in ('FRIENDS', 'LOCAL_LEAGUE', 'FRIENDS_LEAGUE') and o.deck_id is not null and d.deck_type = 'STANDARD'
                GROUP BY d.sas
            ),
            sas_losses AS (
                SELECT d.sas, COUNT(g.id) AS losses
                FROM keyforge_decks d
                LEFT JOIN keyforge_decks_ownership o on d.id = o.deck_id
                LEFT JOIN keyforge_games g ON d.id = g.loser_deck and g.approved is true
                where d.sas > 29 and g.competition in ('FRIENDS', 'LOCAL_LEAGUE', 'FRIENDS_LEAGUE') and o.deck_id is not null and d.deck_type = 'STANDARD'
                GROUP BY d.sas
            )
            SELECT 
                COALESCE(w.sas, l.sas) AS sas,
                COALESCE(w.wins, 0) AS wins,
                COALESCE(l.losses, 0) AS losses,
                ROUND(COALESCE(w.wins, 0)::NUMERIC / NULLIF((COALESCE(w.wins, 0) + COALESCE(l.losses, 0)), 0) * 100, 2) AS winrate
            FROM sas_wins w
            FULL OUTER JOIN sas_losses l ON w.sas = l.sas
            ORDER BY  sas;"
        )->fetchAllAssociative();

        $wrBySas = [];
        foreach ($wrBySasResult as $r) {
            $wrBySas[$r['sas']] = $r;
        }

        $wrByHouseResult = $this->connection->executeQuery(
            'WITH house_wins AS (
                SELECT house, COUNT(g.id) AS wins
                FROM keyforge_decks d
                LEFT JOIN keyforge_decks_ownership o on d.id = o.deck_id
                CROSS JOIN LATERAL jsonb_array_elements_text(d.houses) AS h(house)
                LEFT JOIN keyforge_games g ON d.id = g.winner_deck and g.approved is true
                WHERE o.deck_id is not null and d.sas > 30 and d.deck_type = \'STANDARD\'
                GROUP BY house
            ),
            house_losses AS (
                SELECT house, COUNT(g.id) AS losses
                FROM keyforge_decks d
                LEFT JOIN keyforge_decks_ownership o on d.id = o.deck_id
                CROSS JOIN LATERAL jsonb_array_elements_text(d.houses) AS h(house)
                LEFT JOIN keyforge_games g ON d.id = g.loser_deck and g.approved is true
                WHERE o.deck_id is not null and d.sas > 30 and d.deck_type = \'STANDARD\'
                GROUP BY house
            )
            SELECT 
                w.house,
                COALESCE(w.wins, 0) AS wins,
                COALESCE(l.losses, 0) AS losses,
                ROUND(COALESCE(w.wins, 0)::NUMERIC / NULLIF((COALESCE(w.wins, 0) + COALESCE(l.losses, 0)), 0) * 100, 2) AS winrate
            FROM house_wins w
            FULL OUTER JOIN house_losses l ON w.house = l.house
            ORDER BY winrate DESC;'
        )->fetchAllAssociative();

        $wrByHouse = [];
        foreach ($wrByHouseResult as $r) {
            $wrByHouse[$r['house']] = $r;
        }

        $avgStatsBySetResult = $this->connection->executeQuery(
            'SELECT d.set,
                ROUND(AVG(expected_amber)::numeric, 1) AS avg_expected_amber,
                ROUND(AVG(creature_control)::numeric, 1) AS avg_creature_control,
                ROUND(AVG(amber_control)::numeric, 1) AS avg_amber_control,
                ROUND(AVG(artifact_control)::numeric, 1) AS avg_artifact_control,
                ROUND(AVG(creature_protection)::numeric, 1) AS avg_creature_protection,
                ROUND(AVG(disruption)::numeric, 1) AS avg_disruption,
                ROUND(AVG(efficiency)::numeric, 1) AS avg_efficiency,
                ROUND(AVG(recursion)::numeric, 1) AS avg_recursion
                FROM keyforge_decks d
                LEFT JOIN keyforge_decks_ownership o on d.id = o.deck_id
                WHERE o.deck_id is not null and d.sas > 30 and d.deck_type = \'STANDARD\'
                GROUP BY d.set
                ORDER BY d.set'
        )->fetchAllAssociative();

        $avgStatsBySet = [];
        foreach ($avgStatsBySetResult as $r) {
            $avgStatsBySet[$r['set']] = $r;
        }

        return [$houses, $sets, $wrBySet, $wrBySas, $wrByHouse, $avgStatsBySet];
    }

    private function map(array $deck): KeyforgeDeck
    {
        $owners = [];
        if (null !== $deck['owners']) {
            $owners = \array_unique(\array_map(static fn (string $id): Uuid => Uuid::from($id), \explode(',', $deck['owners'])));
        }

        $userData = null;
        if (\array_key_exists('stats_from_users', $deck)) {
            $userData = KeyforgeDeckUserData::from(
                Uuid::from($deck['id']),
                null,
                $deck['wins'],
                $deck['losses'],
                $deck['wins_vs_friends'],
                $deck['losses_vs_friends'],
                $deck['wins_vs_users'],
                $deck['losses_vs_users'],
            );
        }

        return new KeyforgeDeck(
            Uuid::from($deck['id']),
            $deck['dok_id'],
            KeyforgeDeckType::from($deck['deck_type']),
            $deck['name'],
            KeyforgeSet::from($deck['set']),
            KeyforgeDeckHouses::from(
                KeyforgeHouse::from(Json::decode($deck['houses'])[0]),
                KeyforgeHouse::from(Json::decode($deck['houses'])[1]),
                KeyforgeHouse::from(Json::decode($deck['houses'])[2]),
            ),
            KeyforgeCards::fromArray(Json::decode($deck['cards'])),
            KeyforgeDeckStats::fromArray($deck),
            Json::decode($deck['tags']),
            $owners,
            $userData,
            Json::decodeNullable($deck['alliance_composition']),
        );
    }
}
