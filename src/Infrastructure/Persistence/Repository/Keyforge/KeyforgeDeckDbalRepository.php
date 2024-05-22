<?php declare(strict_types=1);

namespace AdnanMula\Cards\Infrastructure\Persistence\Repository\Keyforge;

use AdnanMula\Cards\Application\Service\Json;
use AdnanMula\Cards\Domain\Model\Keyforge\KeyforgeDeck;
use AdnanMula\Cards\Domain\Model\Keyforge\KeyforgeDeckRepository;
use AdnanMula\Cards\Domain\Model\Keyforge\ValueObject\KeyforgeDeckData;
use AdnanMula\Cards\Domain\Model\Keyforge\ValueObject\KeyforgeDeckUserData;
use AdnanMula\Cards\Domain\Model\Shared\ValueObject\Uuid;
use AdnanMula\Cards\Infrastructure\Persistence\Repository\DbalRepository;
use AdnanMula\Criteria\Criteria;
use AdnanMula\Criteria\DbalCriteriaAdapter;
use Doctrine\DBAL\Connection;

final class KeyforgeDeckDbalRepository extends DbalRepository implements KeyforgeDeckRepository
{
    private const TABLE = 'keyforge_decks';
    private const TABLE_DATA = 'keyforge_decks_data';
    private const TABLE_USER_DATA = 'keyforge_decks_user_data';
    private const TABLE_PAST_SAS = 'keyforge_decks_past_sas';

    private const FIELD_MAPPING = [
        'id' => 'a.id',
        'owner' => 'c.owner',
    ];

    public function search(Criteria $criteria): array
    {
        $builder = $this->connection->createQueryBuilder();

        $query = $builder->select('a.*, b.*, c.*')
            ->from(self::TABLE, 'a')
            ->innerJoin('a', self::TABLE_DATA, 'b', 'a.id = b.id')
            ->innerJoin('a', self::TABLE_USER_DATA, 'c', 'a.id = c.id');

        (new DbalCriteriaAdapter($builder, self::FIELD_MAPPING))->execute($criteria);

        $result = $query->executeQuery()->fetchAllAssociative();

        return \array_map(fn (array $row) => $this->map($row), $result);
    }

    public function count(Criteria $criteria): int
    {
        $builder = $this->connection->createQueryBuilder();
        $query = $builder->select('COUNT(a.id)')
            ->from(self::TABLE, 'a')
            ->innerJoin('a', self::TABLE_DATA, 'b', 'a.id = b.id')
            ->innerJoin('a', self::TABLE_USER_DATA, 'c', 'a.id = c.id');

        (new DbalCriteriaAdapter($builder, self::FIELD_MAPPING))->execute($criteria);

        return $query->executeQuery()->fetchOne();
    }

    public function byId(Uuid $id): ?KeyforgeDeck
    {
        $result = $this->connection->createQueryBuilder()
            ->select('a.*, b.*, c.*')
            ->from(self::TABLE, 'a')
            ->innerJoin('a', self::TABLE_DATA, 'b', 'a.id = b.id')
            ->innerJoin('a', self::TABLE_USER_DATA, 'c', 'a.id = c.id')
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

    public function byIds(Uuid ...$ids): array
    {
        $result = $this->connection->createQueryBuilder()
            ->select('a.*, b.*, c.*')
            ->from(self::TABLE, 'a')
            ->innerJoin('a', self::TABLE_DATA, 'b', 'a.id = b.id')
            ->innerJoin('a', self::TABLE_USER_DATA, 'c', 'a.id = c.id')
            ->where('a.id in (:ids)')
            ->setParameter('ids', \array_map(static fn (Uuid $id) => $id->value(), $ids), Connection::PARAM_STR_ARRAY)
            ->executeQuery()
            ->fetchAllAssociative();

        if ([] === $result || false === $result) {
            return [];
        }

        return \array_map(fn (array $deck) => $this->map($deck), $result);
    }

    public function byNames(string ...$decks): array
    {
        $result = $this->connection->createQueryBuilder()
            ->select('a.*, b.*, c.*')
            ->from(self::TABLE, 'a')
            ->innerJoin('a', self::TABLE_DATA, 'b', 'a.id = b.id')
            ->innerJoin('a', self::TABLE_USER_DATA, 'c', 'a.id = c.id')
            ->where('a.name in (:decks)')
            ->setParameter('decks', $decks, Connection::PARAM_STR_ARRAY)
            ->executeQuery()
            ->fetchAllAssociative();

        if ([] === $result || false === $result) {
            return [];
        }

        return \array_map(fn (array $deck) => $this->map($deck), $result);
    }

    public function save(KeyforgeDeck $deck, bool $updateUserData = false): void
    {
        $stmt = $this->connection->prepare(
            \sprintf(
                '
                    INSERT INTO %s (id, name, set, houses)
                    VALUES (:id, :name, :set, :houses)
                    ON CONFLICT (id) DO NOTHING
                ',
                self::TABLE,
            ),
        );

        $stmt->bindValue(':id', $deck->id()->value());
        $stmt->bindValue(':name', $deck->data()->name);
        $stmt->bindValue(':set', $deck->data()->set->name);
        $stmt->bindValue(':houses', Json::encode($deck->data()->houses->value()));

        $stmt->executeStatement();

        $this->saveDeckData($deck->data());

        if ($updateUserData) {
            $this->saveDeckUserData($deck->userData());
        }
    }

    public function saveDeckData(KeyforgeDeckData $data): void
    {
        $stmt = $this->connection->prepare(
            \sprintf(
                '
                INSERT INTO %s (
                    id,
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
                    synergy_rating,
                    anti_synergy_rating,
                    aerc_score,
                    aerc_version,
                    sas_version,
                    sas_percentile,
                    previous_sas_rating,
                    previous_major_sas_rating,
                    extra_data,
                    last_sas_update
                ) VALUES (
                    :id,	
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
                    :synergy_rating,	
                    :anti_synergy_rating,	
                    :aerc_score,	
                    :aerc_version,	
                    :sas_version,	
                    :sas_percentile,	
                    :previous_sas_rating,	
                    :previous_major_sas_rating,	
                    :extra_data,	
                    :last_sas_update
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
                    synergy_rating = :synergy_rating,	
                    anti_synergy_rating = :anti_synergy_rating,
                    aerc_score = :aerc_score,
                    aerc_version = :aerc_version,
                    sas_version = :sas_version,
                    sas_percentile = :sas_percentile,	
                    previous_sas_rating = :previous_sas_rating,
                    previous_major_sas_rating = :previous_major_sas_rating,
                    extra_data = :extra_data,
                    last_sas_update = :last_sas_update
                ',
                self::TABLE_DATA,
            ),
        );

        $stmt->bindValue(':id', $data->id);
        $stmt->bindValue(':dok_id', $data->dokId);
        $stmt->bindValue(':amber_control', $data->stats->amberControl);
        $stmt->bindValue(':artifact_control', $data->stats->artifactControl);
        $stmt->bindValue(':expected_amber', $data->stats->expectedAmber);
        $stmt->bindValue(':creature_control', $data->stats->creatureControl);
        $stmt->bindValue(':efficiency', $data->stats->efficiency);
        $stmt->bindValue(':recursion', $data->stats->recursion);
        $stmt->bindValue(':disruption', $data->stats->disruption);
        $stmt->bindValue(':effective_power', $data->stats->effectivePower);
        $stmt->bindValue(':creature_protection', $data->stats->creatureProtection);
        $stmt->bindValue(':other', $data->stats->other);
        $stmt->bindValue(':raw_amber', $data->stats->rawAmber);
        $stmt->bindValue(':total_power', $data->stats->totalPower);
        $stmt->bindValue(':total_armor', $data->stats->totalArmor);
        $stmt->bindValue(':efficiency_bonus', $data->stats->efficiencyBonus);
        $stmt->bindValue(':creature_count', $data->stats->creatureCount);
        $stmt->bindValue(':action_count', $data->stats->actionCount);
        $stmt->bindValue(':artifact_count', $data->stats->artifactCount);
        $stmt->bindValue(':upgrade_count', $data->stats->upgradeCount);
        $stmt->bindValue(':card_draw_count', $data->stats->cardDrawCount);
        $stmt->bindValue(':card_archive_count', $data->stats->cardArchiveCount);
        $stmt->bindValue(':key_cheat_count', $data->stats->keyCheatCount);
        $stmt->bindValue(':synergy_rating', $data->stats->synergyRating);
        $stmt->bindValue(':anti_synergy_rating', $data->stats->antiSynergyRating);
        $stmt->bindValue(':sas', $data->stats->sas);
        $stmt->bindValue(':previous_sas_rating', $data->stats->previousSasRating);
        $stmt->bindValue(':previous_major_sas_rating', $data->stats->previousMajorSasRating);
        $stmt->bindValue(':sas_percentile', $data->stats->sasPercentile);
        $stmt->bindValue(':aerc_score', $data->stats->aercScore);
        $stmt->bindValue(':aerc_version', $data->stats->aercVersion);
        $stmt->bindValue(':sas_version', $data->stats->sasVersion);
        $stmt->bindValue(':extra_data', Json::encode($data->rawData));
        $stmt->bindValue(':last_sas_update', $data->stats->lastSasUpdate->format(\DateTimeInterface::ATOM));

        $stmt->executeStatement();
    }

    public function saveDeckUserData(KeyforgeDeckUserData $data): void
    {
        $stmt = $this->connection->prepare(
            \sprintf(
                '
                INSERT INTO %s (id, wins, losses, owner, notes, tags)
                VALUES (:id, :wins, :losses, :owner, :notes, :tags)
                ON CONFLICT (id) DO UPDATE SET
                    wins = :wins,
                    losses = :losses,
                    owner = :owner,
                    notes = :notes,
                    tags = :tags
                ',
                self::TABLE_USER_DATA,
            ),
        );

        $stmt->bindValue(':id', $data->id->value());
        $stmt->bindValue(':wins', $data->wins);
        $stmt->bindValue(':losses', $data->losses);
        $stmt->bindValue(':owner', $data->owner?->value());
        $stmt->bindValue(':notes', $data->notes);
        $stmt->bindValue(':tags', Json::encode($data->tags));

        $stmt->executeStatement();
    }

    private function map(array $deck): KeyforgeDeck
    {
        $data = KeyforgeDeckData::fromDokData(Json::decode($deck['extra_data']));

        $userData = KeyforgeDeckUserData::from(
            Uuid::from($deck['id']),
            null === $deck['owner'] ? null : Uuid::from($deck['owner']),
            $deck['wins'],
            $deck['losses'],
            $deck['notes'],
            Json::decode($deck['tags']),
        );

        return new KeyforgeDeck(Uuid::from($deck['id']), $data, $userData);
    }
}
