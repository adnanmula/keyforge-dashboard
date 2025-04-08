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

        (new DbalCriteriaAdapter($builder, self::FIELD_MAPPING))->execute($criteria);

        $result = $query->executeQuery()->fetchAllAssociative();

        return \array_map(fn (array $row) => $this->map($row), $result);
    }

    public function searchOne(Criteria $criteria): ?KeyforgeDeck
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
            ->from(self::TABLE, 'a')
            ->leftJoin('a', self::TABLE_OWNERSHIP, 'b', 'a.id = b.deck_id');

        (new DbalCriteriaAdapter($builder))->execute($criteria);

        $result = $query->executeQuery()->fetchOne();

        if (false === $result) {
            return 0;
        }

        return  $result;
    }

    public function save(KeyforgeDeck $deck, bool $updateUserData = false): void
    {
        $stmt = $this->connection->prepare(
            \sprintf(
                '
                INSERT INTO %s (
                    id,
                    name,
                    set,
                    houses,
                    dok_id,
                    sas,
                    amber_control,
                    artifact_control,
                    expected_amber,
                    creature_control,
                    efficiency,
                    recursion,
                    disruption,
                    effective_power,
                    creature_protection,
                    other,
                    raw_amber,
                    total_power,
                    total_armor,
                    efficiency_bonus,
                    creature_count,
                    action_count,
                    artifact_count,
                    upgrade_count,
                    card_draw_count,
                    card_archive_count,
                    key_cheat_count,
                    board_clear_count,
                    board_clear_cards,
                    scaling_amber_control_count,
                    scaling_amber_control_cards,
                    synergy_rating,
                    anti_synergy_rating,
                    aerc_score,
                    aerc_version,
                    sas_version,
                    sas_percentile,
                    previous_sas_rating,
                    previous_major_sas_rating,
                    last_sas_update,
                    cards,
                    tags,
                    deck_type
                ) VALUES (
                    :id,
                    :name,
                    :set,
                    :houses,
                    :dok_id,
                    :sas,
                    :amber_control,
                    :artifact_control,
                    :expected_amber,
                    :creature_control,
                    :efficiency,
                    :recursion,
                    :disruption,
                    :effective_power,
                    :creature_protection,
                    :other,
                    :raw_amber,
                    :total_power,
                    :total_armor,
                    :efficiency_bonus,
                    :creature_count,
                    :action_count,
                    :artifact_count,
                    :upgrade_count,
                    :card_draw_count,
                    :card_archive_count,
                    :key_cheat_count,
                    :board_clear_count,
                    :board_clear_cards,
                    :scaling_amber_control_count,
                    :scaling_amber_control_cards,
                    :synergy_rating,
                    :anti_synergy_rating,
                    :aerc_score,
                    :aerc_version,
                    :sas_version,
                    :sas_percentile,
                    :previous_sas_rating,
                    :previous_major_sas_rating,
                    :last_sas_update,
                    :cards,
                    :tags,
                    :deck_type
                ) ON CONFLICT (id) DO UPDATE SET
                    sas = :sas,
                    amber_control = :amber_control,
                    artifact_control = :artifact_control,
                    expected_amber = :expected_amber,
                    creature_control = :creature_control,
                    efficiency = :efficiency,
                    recursion = :recursion,
                    disruption = :disruption,
                    effective_power = :effective_power,
                    creature_protection = :creature_protection,
                    other = :other,
                    raw_amber = :raw_amber,
                    total_power = :total_power,
                    total_armor = :total_armor,
                    efficiency_bonus = :efficiency_bonus,
                    creature_count = :creature_count,
                    action_count = :action_count,
                    artifact_count = :artifact_count,
                    upgrade_count = :upgrade_count,
                    card_draw_count = :card_draw_count,
                    card_archive_count = :card_archive_count,
                    key_cheat_count = :key_cheat_count,
                    board_clear_count = :board_clear_count,
                    board_clear_cards = :board_clear_cards,
                    scaling_amber_control_count = :scaling_amber_control_count,
                    scaling_amber_control_cards = :scaling_amber_control_cards,
                    synergy_rating = :synergy_rating,
                    anti_synergy_rating = :anti_synergy_rating,
                    aerc_score = :aerc_score,
                    aerc_version = :aerc_version,
                    sas_version = :sas_version,
                    sas_percentile = :sas_percentile,
                    previous_sas_rating = :previous_sas_rating,
                    previous_major_sas_rating = :previous_major_sas_rating,
                    last_sas_update = :last_sas_update,
                    cards = :cards,
                    tags = :tags
                ',
                self::TABLE,
            ),
        );

        $stmt->bindValue(':id', $deck->id()->value());
        $stmt->bindValue(':name', $deck->name());
        $stmt->bindValue(':set', $deck->set()->name);
        $stmt->bindValue(':houses', Json::encode($deck->houses()->value()));
        $stmt->bindValue(':dok_id', $deck->dokId());
        $stmt->bindValue(':amber_control', $deck->stats()->amberControl);
        $stmt->bindValue(':artifact_control', $deck->stats()->artifactControl);
        $stmt->bindValue(':expected_amber', $deck->stats()->expectedAmber);
        $stmt->bindValue(':creature_control', $deck->stats()->creatureControl);
        $stmt->bindValue(':efficiency', $deck->stats()->efficiency);
        $stmt->bindValue(':recursion', $deck->stats()->recursion);
        $stmt->bindValue(':disruption', $deck->stats()->disruption);
        $stmt->bindValue(':effective_power', $deck->stats()->effectivePower);
        $stmt->bindValue(':creature_protection', $deck->stats()->creatureProtection);
        $stmt->bindValue(':other', $deck->stats()->other);
        $stmt->bindValue(':raw_amber', $deck->stats()->rawAmber);
        $stmt->bindValue(':total_power', $deck->stats()->totalPower);
        $stmt->bindValue(':total_armor', $deck->stats()->totalArmor);
        $stmt->bindValue(':efficiency_bonus', $deck->stats()->efficiencyBonus);
        $stmt->bindValue(':creature_count', $deck->stats()->creatureCount);
        $stmt->bindValue(':action_count', $deck->stats()->actionCount);
        $stmt->bindValue(':artifact_count', $deck->stats()->artifactCount);
        $stmt->bindValue(':upgrade_count', $deck->stats()->upgradeCount);
        $stmt->bindValue(':card_draw_count', $deck->stats()->cardDrawCount);
        $stmt->bindValue(':card_archive_count', $deck->stats()->cardArchiveCount);
        $stmt->bindValue(':key_cheat_count', $deck->stats()->keyCheatCount);
        $stmt->bindValue(':board_clear_count', $deck->stats()->boardClearCount);
        $stmt->bindValue(':board_clear_cards', Json::encode($deck->stats()->boardClearCards));
        $stmt->bindValue(':scaling_amber_control_count', $deck->stats()->scalingAmberControlCount);
        $stmt->bindValue(':scaling_amber_control_cards', Json::encode($deck->stats()->scalingAmberControlCards));
        $stmt->bindValue(':synergy_rating', $deck->stats()->synergyRating);
        $stmt->bindValue(':anti_synergy_rating', $deck->stats()->antiSynergyRating);
        $stmt->bindValue(':sas', $deck->stats()->sas);
        $stmt->bindValue(':previous_sas_rating', $deck->stats()->previousSasRating);
        $stmt->bindValue(':previous_major_sas_rating', $deck->stats()->previousMajorSasRating);
        $stmt->bindValue(':sas_percentile', $deck->stats()->sasPercentile);
        $stmt->bindValue(':aerc_score', $deck->stats()->aercScore);
        $stmt->bindValue(':aerc_version', $deck->stats()->aercVersion);
        $stmt->bindValue(':sas_version', $deck->stats()->sasVersion);
        $stmt->bindValue(':last_sas_update', $deck->stats()->lastSasUpdate?->format(\DateTimeInterface::ATOM));
        $stmt->bindValue(':cards', Json::encode($deck->cards()->jsonSerialize()));
        $stmt->bindValue(':tags', Json::encode($deck->tags()));
        $stmt->bindValue(':deck_type', $deck->type()->value);

        $stmt->executeStatement();
    }

    public function addOwner(Uuid $deckId, Uuid $userId): void
    {
        $stmt = $this->connection->prepare(
            \sprintf('
                INSERT INTO %s (deck_id, user_id, notes, user_tags)
                VALUES (:deck_id, :user_id, :notes, :user_tags)
                ON CONFLICT (deck_id, user_id) DO NOTHING
                ',
                self::TABLE_OWNERSHIP,
            ),
        );

        $stmt->bindValue(':deck_id', $deckId->value());
        $stmt->bindValue(':user_id', $userId->value());
        $stmt->bindValue(':notes', '');
        $stmt->bindValue(':user_tags', Json::encode([]));

        $stmt->executeStatement();
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
