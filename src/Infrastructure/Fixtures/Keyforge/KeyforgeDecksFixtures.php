<?php declare(strict_types=1);

namespace AdnanMula\Cards\Infrastructure\Fixtures\Keyforge;

use AdnanMula\Cards\Domain\Model\Keyforge\KeyforgeDeck;
use AdnanMula\Cards\Domain\Model\Keyforge\ValueObject\KeyforgeDeckHouses;
use AdnanMula\Cards\Domain\Model\Keyforge\ValueObject\KeyforgeHouse;
use AdnanMula\Cards\Domain\Model\Keyforge\ValueObject\KeyforgeSet;
use AdnanMula\Cards\Domain\Model\Shared\ValueObject\UuidValueObject;
use AdnanMula\Cards\Domain\Service\Persistence\Fixture;
use AdnanMula\Cards\Infrastructure\Fixtures\DbalFixture;

final class KeyforgeDecksFixtures extends DbalFixture implements Fixture
{
    public const FIXTURE_KEYFORGE_DECK_1_ID = '10ff6ac7-c6c9-444b-a1aa-10fe87c3c524';
    public const FIXTURE_KEYFORGE_DECK_2_ID = '496b4258-b02c-4270-9918-4fd9c3366986';
    public const FIXTURE_KEYFORGE_DECK_3_ID = 'aa99749f-79b3-4040-8cd7-5c824cf3da3b';
    public const FIXTURE_KEYFORGE_DECK_4_ID = 'deb90365-d69e-4ed4-9bf9-796320230ebb';

    private const TABLE = 'keyforge_decks';

    private bool $loaded = false;

    public function load(): void
    {
        $this->save(
            new KeyforgeDeck(
                UuidValueObject::from(self::FIXTURE_KEYFORGE_DECK_1_ID),
                'Parker la Sedienta',
                KeyforgeSet::CotA,
                KeyforgeDeckHouses::from(
                    KeyforgeHouse::UNTAMED,
                    KeyforgeHouse::SHADOWS,
                    KeyforgeHouse::LOGOS,
                ),
                71,
                4,
                4,
            ),
        );

        $this->save(
            new KeyforgeDeck(
                UuidValueObject::from(self::FIXTURE_KEYFORGE_DECK_2_ID),
                'Lydia la Inacabable de la Colmena',
                KeyforgeSet::AoA,
                KeyforgeDeckHouses::from(
                    KeyforgeHouse::LOGOS,
                    KeyforgeHouse::SHADOWS,
                    KeyforgeHouse::SANCTUM,
                ),
                63,
                3,
                0,
            ),
        );

        $this->save(
            new KeyforgeDeck(
                UuidValueObject::from(self::FIXTURE_KEYFORGE_DECK_3_ID),
                'Harrison "Sátiro", Rebelde del Foro',
                KeyforgeSet::MM,
                KeyforgeDeckHouses::from(
                    KeyforgeHouse::DIS,
                    KeyforgeHouse::LOGOS,
                    KeyforgeHouse::SANCTUM,
                ),
                72,
                7,
                5,
            ),
        );

        $this->save(
            new KeyforgeDeck(
                UuidValueObject::from(self::FIXTURE_KEYFORGE_DECK_4_ID),
                'Cassiopeia la Artera',
                KeyforgeSet::DT,
                KeyforgeDeckHouses::from(
                    KeyforgeHouse::SANCTUM,
                    KeyforgeHouse::SAURIAN,
                    KeyforgeHouse::STAR_ALLIANCE,
                ),
                61,
                1,
                8,
            ),
        );

        $this->loaded = true;
    }

    public function isLoaded(): bool
    {
        return $this->loaded;
    }

    public function dependants(): array
    {
        return [];
    }

    private function save(KeyforgeDeck $deck): void
    {
        $stmt = $this->connection->prepare(
            \sprintf(
                '
                    INSERT INTO %s (id, name, set, houses, sas, wins, losses)
                    VALUES (:id, :name, :set, :houses, :sas, :wins, :losses)
                    ON CONFLICT (id) DO UPDATE SET
                        id = :id,
                        name = :name,
                        set = :set,
                        houses = :houses,
                        sas = :sas,
                        wins = :wins,
                        losses = :losses
                    ',
                self::TABLE,
            ),
        );

        $stmt->bindValue(':id', $deck->id()->value());
        $stmt->bindValue(':name', $deck->name());
        $stmt->bindValue(':set', $deck->set()->name);
        $stmt->bindValue(':houses', \json_encode($deck->houses()->value()));
        $stmt->bindValue(':sas', $deck->sas());
        $stmt->bindValue(':wins', $deck->wins());
        $stmt->bindValue(':losses', $deck->losses());

        $stmt->execute();
    }
}
