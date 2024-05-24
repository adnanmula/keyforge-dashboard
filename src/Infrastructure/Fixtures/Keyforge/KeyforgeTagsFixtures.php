<?php declare(strict_types=1);

namespace AdnanMula\Cards\Infrastructure\Fixtures\Keyforge;

use AdnanMula\Cards\Application\Service\Json;
use AdnanMula\Cards\Domain\Model\Keyforge\Deck\KeyforgeDeckTag;
use AdnanMula\Cards\Domain\Model\Keyforge\Deck\Tag\KeyforgeTagActionCountHigh;
use AdnanMula\Cards\Domain\Model\Keyforge\Deck\Tag\KeyforgeTagAmberBonusHigh;
use AdnanMula\Cards\Domain\Model\Keyforge\Deck\Tag\KeyforgeTagAmberBonusLow;
use AdnanMula\Cards\Domain\Model\Keyforge\Deck\Tag\KeyforgeTagAmberControlHigh;
use AdnanMula\Cards\Domain\Model\Keyforge\Deck\Tag\KeyforgeTagAmberControlLow;
use AdnanMula\Cards\Domain\Model\Keyforge\Deck\Tag\KeyforgeTagAmberExpectedHigh;
use AdnanMula\Cards\Domain\Model\Keyforge\Deck\Tag\KeyforgeTagAmberExpectedLow;
use AdnanMula\Cards\Domain\Model\Keyforge\Deck\Tag\KeyforgeTagAntiSynergyHigh;
use AdnanMula\Cards\Domain\Model\Keyforge\Deck\Tag\KeyforgeTagArchiveCardCountHigh;
use AdnanMula\Cards\Domain\Model\Keyforge\Deck\Tag\KeyforgeTagArtifactControlHigh;
use AdnanMula\Cards\Domain\Model\Keyforge\Deck\Tag\KeyforgeTagArtifactControlLow;
use AdnanMula\Cards\Domain\Model\Keyforge\Deck\Tag\KeyforgeTagArtifactCountHigh;
use AdnanMula\Cards\Domain\Model\Keyforge\Deck\Tag\KeyforgeTagCreatureControlHigh;
use AdnanMula\Cards\Domain\Model\Keyforge\Deck\Tag\KeyforgeTagCreatureControlLow;
use AdnanMula\Cards\Domain\Model\Keyforge\Deck\Tag\KeyforgeTagCreatureCountHigh;
use AdnanMula\Cards\Domain\Model\Keyforge\Deck\Tag\KeyforgeTagCreatureProtectionHigh;
use AdnanMula\Cards\Domain\Model\Keyforge\Deck\Tag\KeyforgeTagDisruptionHigh;
use AdnanMula\Cards\Domain\Model\Keyforge\Deck\Tag\KeyforgeTagEffectivePowerHigh;
use AdnanMula\Cards\Domain\Model\Keyforge\Deck\Tag\KeyforgeTagEfficiencyHigh;
use AdnanMula\Cards\Domain\Model\Keyforge\Deck\Tag\KeyforgeTagEfficiencyLow;
use AdnanMula\Cards\Domain\Model\Keyforge\Deck\Tag\KeyforgeTagHasAnomaly;
use AdnanMula\Cards\Domain\Model\Keyforge\Deck\Tag\KeyforgeTagHasBoardWipes;
use AdnanMula\Cards\Domain\Model\Keyforge\Deck\Tag\KeyforgeTagHasKeyCheats;
use AdnanMula\Cards\Domain\Model\Keyforge\Deck\Tag\KeyforgeTagHasLegacy;
use AdnanMula\Cards\Domain\Model\Keyforge\Deck\Tag\KeyforgeTagHasMaverick;
use AdnanMula\Cards\Domain\Model\Keyforge\Deck\Tag\KeyforgeTagHasScalingAmberControl;
use AdnanMula\Cards\Domain\Model\Keyforge\Deck\Tag\KeyforgeTagRecursionHigh;
use AdnanMula\Cards\Domain\Model\Keyforge\Deck\Tag\KeyforgeTagSynergyHigh;
use AdnanMula\Cards\Domain\Model\Keyforge\Deck\Tag\KeyforgeTagUpgradeCountHigh;
use AdnanMula\Cards\Domain\Service\Persistence\Fixture;
use AdnanMula\Cards\Infrastructure\Fixtures\DbalFixture;
use Doctrine\DBAL\ParameterType;

final class KeyforgeTagsFixtures extends DbalFixture implements Fixture
{
    private const TABLE = 'keyforge_tags';

    private bool $loaded = false;

    public function load(): void
    {
        $this->save(new KeyforgeTagActionCountHigh());
        $this->save(new KeyforgeTagAmberBonusHigh());
        $this->save(new KeyforgeTagAmberBonusLow());
        $this->save(new KeyforgeTagAmberControlHigh());
        $this->save(new KeyforgeTagAmberControlLow());
        $this->save(new KeyforgeTagAmberExpectedHigh());
        $this->save(new KeyforgeTagAmberExpectedLow());
        $this->save(new KeyforgeTagAntiSynergyHigh());
        $this->save(new KeyforgeTagArchiveCardCountHigh());
        $this->save(new KeyforgeTagArtifactControlHigh());
        $this->save(new KeyforgeTagArtifactControlLow());
        $this->save(new KeyforgeTagArtifactCountHigh());
        $this->save(new KeyforgeTagCreatureControlHigh());
        $this->save(new KeyforgeTagCreatureControlLow());
        $this->save(new KeyforgeTagCreatureCountHigh());
        $this->save(new KeyforgeTagCreatureProtectionHigh());
        $this->save(new KeyforgeTagDisruptionHigh());
        $this->save(new KeyforgeTagEffectivePowerHigh());
        $this->save(new KeyforgeTagEfficiencyHigh());
        $this->save(new KeyforgeTagEfficiencyLow());
        $this->save(new KeyforgeTagHasBoardWipes());
        $this->save(new KeyforgeTagHasAnomaly());
        $this->save(new KeyforgeTagHasKeyCheats());
        $this->save(new KeyforgeTagHasLegacy());
        $this->save(new KeyforgeTagHasMaverick());
        $this->save(new KeyforgeTagHasScalingAmberControl());
        $this->save(new KeyforgeTagRecursionHigh());
        $this->save(new KeyforgeTagSynergyHigh());
        $this->save(new KeyforgeTagUpgradeCountHigh());

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

    private function save(KeyforgeDeckTag $tag): void
    {
        $stmt = $this->connection->prepare(
            \sprintf(
                '
                    INSERT INTO %s (id, name, visibility, style, type, archived)
                    VALUES (:id, :name, :visibility, :style, :type, :archived)
                    ON CONFLICT (id) DO UPDATE SET
                        id = :id,
                        name = :name,
                        visibility = :visibility,
                        style = :style,
                        type = :type,
                        archived = :archived
                    ',
                self::TABLE,
            ),
        );

        $stmt->bindValue(':id', $tag->id->value());
        $stmt->bindValue(':name', Json::encode($tag->name));
        $stmt->bindValue(':visibility', $tag->visibility->name);
        $stmt->bindValue(':style', Json::encode($tag->style));
        $stmt->bindValue(':type', $tag->type->name);
        $stmt->bindValue(':archived', $tag->archived, ParameterType::BOOLEAN);

        $stmt->executeStatement();
    }
}
