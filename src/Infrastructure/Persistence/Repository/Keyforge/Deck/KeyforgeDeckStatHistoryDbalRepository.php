<?php declare(strict_types=1);

namespace AdnanMula\Cards\Infrastructure\Persistence\Repository\Keyforge\Deck;

use AdnanMula\Cards\Domain\Model\Keyforge\Deck\KeyforgeDeckStatHistoryRepository;
use AdnanMula\Cards\Domain\Model\Keyforge\Deck\ValueObject\KeyforgeDeckStatHistory;
use AdnanMula\Cards\Domain\Model\Shared\ValueObject\Uuid;
use AdnanMula\Cards\Infrastructure\Persistence\Repository\DbalRepository;
use Doctrine\DBAL\ArrayParameterType;

final class KeyforgeDeckStatHistoryDbalRepository extends DbalRepository implements KeyforgeDeckStatHistoryRepository
{
    private const TABLE = 'keyforge_decks_data_history';

    public function save(KeyforgeDeckStatHistory $data): void
    {
        $stmt = $this->connection->prepare(
            \sprintf(
                '
                INSERT INTO %s (
                    dok_reference,
                    deck_id,
                    dok_deck_id,
                    sas,
                    aerc_score,
                    aerc_version,
                    expected_amber,
                    amber_control,
                    creature_control,
                    artifact_control,
                    efficiency,
                    recursion,
                    creature_protection,
                    disruption,
                    other,
                    effective_power,
                    synergy_rating,
                    antisynergy_rating,
                    updated_at
                ) VALUES (
                    :dok_reference,
                    :deck_id,
                    :dok_deck_id,
                    :sas,
                    :aerc_score,
                    :aerc_version,
                    :expected_amber,
                    :amber_control,
                    :creature_control,
                    :artifact_control,
                    :efficiency,
                    :recursion,
                    :creature_protection,
                    :disruption,
                    :other,
                    :effective_power,
                    :synergy_rating,
                    :antisynergy_rating,
                    :updated_at
                ) ON CONFLICT (dok_reference) DO UPDATE SET
                    sas = :sas,
                    aerc_score = :aerc_score,
                    aerc_version = :aerc_version,
                    expected_amber = :expected_amber,
                    amber_control = :amber_control,
                    creature_control = :creature_control,
                    artifact_control = :artifact_control,
                    efficiency = :efficiency,
                    recursion = :recursion,
                    creature_protection = :creature_protection,
                    disruption = :disruption,
                    other = :other,
                    effective_power = :effective_power,
                    synergy_rating = :synergy_rating,
                    antisynergy_rating = :antisynergy_rating,
                    updated_at = :updated_at
                ',
                self::TABLE,
            ),
        );

        $stmt->bindValue(':dok_reference', $data->dokReference);
        $stmt->bindValue(':deck_id', $data->deckId);
        $stmt->bindValue(':dok_deck_id', $data->dokDeckId);
        $stmt->bindValue(':sas', $data->sas);
        $stmt->bindValue(':aerc_score', $data->aercScore);
        $stmt->bindValue(':aerc_version', $data->aercVersion);
        $stmt->bindValue(':expected_amber', $data->expectedAmber);
        $stmt->bindValue(':amber_control', $data->amberControl);
        $stmt->bindValue(':creature_control', $data->creatureControl);
        $stmt->bindValue(':artifact_control', $data->artifactControl);
        $stmt->bindValue(':efficiency', $data->efficiency);
        $stmt->bindValue(':recursion', $data->recursion);
        $stmt->bindValue(':creature_protection', $data->creatureProtection);
        $stmt->bindValue(':disruption', $data->disruption);
        $stmt->bindValue(':other', $data->other);
        $stmt->bindValue(':effective_power', $data->effectivePower);
        $stmt->bindValue(':synergy_rating', $data->synergyRating);
        $stmt->bindValue(':antisynergy_rating', $data->antiSynergyRating);
        $stmt->bindValue(':updated_at', $data->updatedAt->format(\DateTimeInterface::ATOM));

        $stmt->executeStatement();
    }

    public function byDeckIds(Uuid ...$ids): array
    {
        $result = $this->connection->createQueryBuilder()
            ->select('a.*')
            ->from(self::TABLE, 'a')
            ->where('a.deck_id in (:ids)')
            ->setParameter('ids', \array_map(static fn (Uuid $id) => $id->value(), $ids), ArrayParameterType::STRING)
            ->orderBy('a.updated_at', 'asc')
            ->executeQuery()
            ->fetchAllAssociative();

        if ([] === $result || false === $result) {
            return [];
        }

        $indexedResult = [];

        foreach ($result as $stats) {
            $indexedResult[$stats['deck_id']][] = $this->map($stats);
        }

        foreach ($indexedResult as $stats) {
            $prevSas = null;

            /** @var KeyforgeDeckStatHistory $stat */
            foreach ($stats as $stat) {
                $currentSas = $stat->sas;
                $stat->setSasModified($currentSas - ($prevSas ?? $currentSas));
                $prevSas = $currentSas;
            }
        }

        return $indexedResult;
    }

    private function map(array $deck): KeyforgeDeckStatHistory
    {
        return KeyforgeDeckStatHistory::fromArray($deck);
    }
}
