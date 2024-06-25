<?php declare(strict_types=1);

namespace AdnanMula\Cards\Infrastructure\Persistence\Repository\Keyforge\Deck;

use AdnanMula\Cards\Application\Service\Json;
use AdnanMula\Cards\Domain\Model\Keyforge\Deck\KeyforgeDeck;
use AdnanMula\Cards\Domain\Model\Keyforge\Deck\KeyforgeDeckRepository;
use AdnanMula\Cards\Domain\Model\Keyforge\Deck\ValueObject\KeyforgeCards;
use AdnanMula\Cards\Domain\Model\Keyforge\Deck\ValueObject\KeyforgeDeckHouses;
use AdnanMula\Cards\Domain\Model\Keyforge\Deck\ValueObject\KeyforgeDeckStats;
use AdnanMula\Cards\Domain\Model\Keyforge\Deck\ValueObject\KeyforgeDeckUserData;
use AdnanMula\Cards\Domain\Model\Keyforge\Deck\ValueObject\KeyforgeHouse;
use AdnanMula\Cards\Domain\Model\Keyforge\Deck\ValueObject\KeyforgeSet;
use AdnanMula\Cards\Domain\Model\Shared\ValueObject\Uuid;
use AdnanMula\Cards\Infrastructure\Persistence\Repository\DbalRepository;
use AdnanMula\Criteria\Criteria;
use AdnanMula\Criteria\DbalCriteriaAdapter;

final class KeyforgeDeckDbalRepository extends DbalRepository implements KeyforgeDeckRepository
{
    private const TABLE = 'keyforge_decks';
    private const TABLE_OWNERSHIP = 'keyforge_decks_ownership';
    private const TABLE_USER_DATA = 'keyforge_decks_user_data';

    private const FIELD_MAPPING = [
        'id' => 'a.id',
        'owner' => 'b.user_id',
        'wins' => 'wins',
        'losses' => 'losses',
    ];

    public function search(Criteria $criteria): array
    {
        $builder = $this->connection->createQueryBuilder();

        $query = $builder->select('a.*')
            ->addSelect("string_agg(b.user_id::varchar, ',') as owners")
            ->addSelect("string_agg(c.user_id::varchar, ',') as stats_from_users")
            ->addSelect('COALESCE(SUM(c.wins), 0) as wins, COALESCE(SUM(c.losses), 0) as losses')
            ->addSelect('COALESCE(SUM(c.wins_vs_friends), 0) as wins_vs_friends, COALESCE(SUM(c.losses_vs_friends), 0) as losses_vs_friends')
            ->addSelect('COALESCE(SUM(c.wins_vs_users), 0) as wins_vs_users, COALESCE(SUM(c.losses_vs_users), 0) as losses_vs_users')
            ->from(self::TABLE, 'a')
            ->leftJoin('a', self::TABLE_OWNERSHIP, 'b', 'a.id = b.deck_id')
            ->leftJoin('a', self::TABLE_USER_DATA, 'c', 'a.id = c.deck_id')
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
            ->leftJoin('a', self::TABLE_OWNERSHIP, 'b', 'a.id = b.deck_id')
            ->leftJoin('a', self::TABLE_USER_DATA, 'c', 'a.id = c.deck_id')
            ->groupBy('a.id');

        (new DbalCriteriaAdapter($builder))->execute($criteria);

        return $query->executeQuery()->fetchOne();
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
                    tags
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
                    :tags
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
        $stmt->bindValue(':last_sas_update', $deck->stats()->lastSasUpdate->format(\DateTimeInterface::ATOM));
        $stmt->bindValue(':cards', Json::encode($deck->cards()->jsonSerialize()));
        $stmt->bindValue(':tags', Json::encode($deck->tags()));

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

    /** @return array<array{deck_id: string, user_id: string, notes: string}> */
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

    private function map(array $deck): KeyforgeDeck
    {
        $owners = [];
        if (null !== $deck['owners']) {
            $owners = array_map(static fn (string $id): Uuid => Uuid::from($id), \explode(',', $deck['owners']));
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
        );
    }
}
