<?php declare(strict_types=1);

namespace AdnanMula\Cards\Infrastructure\Persistence\Repository\Keyforge\Deck;

use AdnanMula\Cards\Application\Service\Json;
use AdnanMula\Cards\Domain\Model\Keyforge\Deck\KeyforgeDeckAllianceRepository;
use AdnanMula\Cards\Domain\Model\Shared\ValueObject\Uuid;
use AdnanMula\Cards\Infrastructure\Persistence\Repository\DbalRepository;

final class KeyforgeDeckAllianceDbalRepository extends DbalRepository implements KeyforgeDeckAllianceRepository
{
    private const string TABLE = 'keyforge_decks';

    public function saveComposition(Uuid $id, array $composition): void
    {
        $stmt = $this->connection->prepare(
            \sprintf(
                'UPDATE %s SET alliance_composition = :composition WHERE id = :id',
                self::TABLE,
            ),
        );

        $stmt->bindValue(':id', $id->value());
        $stmt->bindValue(':composition', Json::encode($composition));

        $stmt->executeStatement();
    }

    public function isAlreadyImported(string $id1, string $house1, string $id2, string $house2, string $id3, string $house3, ?string $extraCardType, ?string $extraCard): bool
    {
        $query = \sprintf(
            "SELECT id FROM %s
                WHERE EXISTS (
                    SELECT 1
                    FROM jsonb_array_elements(alliance_composition->'pods') AS elem
                    WHERE elem->>'keyforgeId' = :id1
                      AND elem->>'house' = :house1
                )
                AND EXISTS (
                    SELECT 1
                    FROM jsonb_array_elements(alliance_composition->'pods') AS elem
                    WHERE elem->>'keyforgeId' = :id2
                      AND elem->>'house' = :house2
                )
                AND EXISTS (
                    SELECT 1
                    FROM jsonb_array_elements(alliance_composition->'pods') AS elem
                    WHERE elem->>'keyforgeId' = :id3
                      AND elem->>'house' = :house3
                )",
            self::TABLE,
        );

        if ($extraCardType === 'Token' && $extraCard !== null) {
            $query .= " AND EXISTS (
                SELECT 1
                FROM jsonb_array_elements(alliance_composition->'extraCards') AS elem
                WHERE elem->>'name' = :extraCard
            )";
        }

        if ($extraCardType === 'Prophecies' && $extraCard !== null) {
            $query .= " AND EXISTS (
                SELECT 1
                FROM jsonb_array_elements(alliance_composition->'extraCards') AS elem
                WHERE elem->>'name' = :prophecy1
            ) AND EXISTS (
                SELECT 1
                FROM jsonb_array_elements(alliance_composition->'extraCards') AS elem
                WHERE elem->>'name' = :prophecy2
            ) AND EXISTS (
                SELECT 1
                FROM jsonb_array_elements(alliance_composition->'extraCards') AS elem
                WHERE elem->>'name' = :prophecy3
            ) AND EXISTS (
                SELECT 1
                FROM jsonb_array_elements(alliance_composition->'extraCards') AS elem
                WHERE elem->>'name' = :prophecy4
            )";
        }

        $stmt = $this->connection->prepare($query);

        $stmt->bindValue('id1', $id1);
        $stmt->bindValue('house1', $house1);
        $stmt->bindValue('id2', $id2);
        $stmt->bindValue('house2', $house2);
        $stmt->bindValue('id3', $id3);
        $stmt->bindValue('house3', $house3);

        if ($extraCardType === 'Token' && $extraCard !== null) {
            $stmt->bindValue('extraCard', $extraCard);
        }

        if ($extraCardType === 'Prophecies' && $extraCard !== null) {
            $propheciesStmt = $this->connection->prepare("SELECT cards FROM keyforge_decks WHERE id = :id");
            $propheciesStmt->bindValue('id', $extraCard);
            $prophecies = json_decode($propheciesStmt->executeQuery()->fetchOne(), true)['extraCards'];

            $stmt->bindValue('prophecy1', $prophecies[0]['name']);;
            $stmt->bindValue('prophecy2', $prophecies[0]['name']);;
            $stmt->bindValue('prophecy3', $prophecies[0]['name']);;
            $stmt->bindValue('prophecy4', $prophecies[0]['name']);;
        }

        return false !== $stmt->executeQuery()->fetchOne();
    }
}
