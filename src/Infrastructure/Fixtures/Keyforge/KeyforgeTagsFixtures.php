<?php declare(strict_types=1);

namespace AdnanMula\Cards\Infrastructure\Fixtures\Keyforge;

use AdnanMula\Cards\Application\Service\Json;
use AdnanMula\Cards\Domain\Model\Keyforge\KeyforgeTag;
use AdnanMula\Cards\Domain\Model\Keyforge\Tag\KeyforgeTagActionCountHigh;
use AdnanMula\Cards\Domain\Model\Keyforge\Tag\KeyforgeTagAmberBonusHigh;
use AdnanMula\Cards\Domain\Model\Keyforge\Tag\KeyforgeTagAmberBonusLow;
use AdnanMula\Cards\Domain\Model\Keyforge\Tag\KeyforgeTagAmberControlHigh;
use AdnanMula\Cards\Domain\Model\Keyforge\Tag\KeyforgeTagAmberControlLow;
use AdnanMula\Cards\Domain\Model\Keyforge\Tag\KeyforgeTagAmberExpectedHigh;
use AdnanMula\Cards\Domain\Model\Keyforge\Tag\KeyforgeTagAmberExpectedLow;
use AdnanMula\Cards\Domain\Model\Keyforge\Tag\KeyforgeTagAntiSynergyHigh;
use AdnanMula\Cards\Domain\Model\Keyforge\Tag\KeyforgeTagArchiveCardCountHigh;
use AdnanMula\Cards\Domain\Model\Keyforge\Tag\KeyforgeTagArtifactControlHigh;
use AdnanMula\Cards\Domain\Model\Keyforge\Tag\KeyforgeTagArtifactControlLow;
use AdnanMula\Cards\Domain\Model\Keyforge\Tag\KeyforgeTagArtifactCountHigh;
use AdnanMula\Cards\Domain\Model\Keyforge\Tag\KeyforgeTagCreatureControlHigh;
use AdnanMula\Cards\Domain\Model\Keyforge\Tag\KeyforgeTagCreatureControlLow;
use AdnanMula\Cards\Domain\Model\Keyforge\Tag\KeyforgeTagCreatureCountHigh;
use AdnanMula\Cards\Domain\Model\Keyforge\Tag\KeyforgeTagCreatureProtectionHigh;
use AdnanMula\Cards\Domain\Model\Keyforge\Tag\KeyforgeTagDisruptionHigh;
use AdnanMula\Cards\Domain\Model\Keyforge\Tag\KeyforgeTagEffectivePowerHigh;
use AdnanMula\Cards\Domain\Model\Keyforge\Tag\KeyforgeTagEfficiencyHigh;
use AdnanMula\Cards\Domain\Model\Keyforge\Tag\KeyforgeTagEfficiencyLow;
use AdnanMula\Cards\Domain\Model\Keyforge\Tag\KeyforgeTagHasAgentZ;
use AdnanMula\Cards\Domain\Model\Keyforge\Tag\KeyforgeTagHasAnomaly;
use AdnanMula\Cards\Domain\Model\Keyforge\Tag\KeyforgeTagHasBoardWipes;
use AdnanMula\Cards\Domain\Model\Keyforge\Tag\KeyforgeTagHasDoubleCards;
use AdnanMula\Cards\Domain\Model\Keyforge\Tag\KeyforgeTagHasFangtoothCavern;
use AdnanMula\Cards\Domain\Model\Keyforge\Tag\KeyforgeTagHasHorseman;
use AdnanMula\Cards\Domain\Model\Keyforge\Tag\KeyforgeTagHasKeyCheats;
use AdnanMula\Cards\Domain\Model\Keyforge\Tag\KeyforgeTagHasLegacy;
use AdnanMula\Cards\Domain\Model\Keyforge\Tag\KeyforgeTagHasMaverick;
use AdnanMula\Cards\Domain\Model\Keyforge\Tag\KeyforgeTagHasRats;
use AdnanMula\Cards\Domain\Model\Keyforge\Tag\KeyforgeTagHasScalingAmberControl;
use AdnanMula\Cards\Domain\Model\Keyforge\Tag\KeyforgeTagHasSins;
use AdnanMula\Cards\Domain\Model\Keyforge\Tag\KeyforgeTagHasTimetraveller;
use AdnanMula\Cards\Domain\Model\Keyforge\Tag\KeyforgeTagOwnerBuko;
use AdnanMula\Cards\Domain\Model\Keyforge\Tag\KeyforgeTagOwnerDani;
use AdnanMula\Cards\Domain\Model\Keyforge\Tag\KeyforgeTagOwnerFran;
use AdnanMula\Cards\Domain\Model\Keyforge\Tag\KeyforgeTagOwnerIsmalelo;
use AdnanMula\Cards\Domain\Model\Keyforge\Tag\KeyforgeTagOwnerNan;
use AdnanMula\Cards\Domain\Model\Keyforge\Tag\KeyforgeTagOwnerNull;
use AdnanMula\Cards\Domain\Model\Keyforge\Tag\KeyforgeTagPercentile05;
use AdnanMula\Cards\Domain\Model\Keyforge\Tag\KeyforgeTagPercentile60;
use AdnanMula\Cards\Domain\Model\Keyforge\Tag\KeyforgeTagPercentile70;
use AdnanMula\Cards\Domain\Model\Keyforge\Tag\KeyforgeTagPercentile80;
use AdnanMula\Cards\Domain\Model\Keyforge\Tag\KeyforgeTagPercentile90;
use AdnanMula\Cards\Domain\Model\Keyforge\Tag\KeyforgeTagPercentile99;
use AdnanMula\Cards\Domain\Model\Keyforge\Tag\KeyforgeTagRecursionHigh;
use AdnanMula\Cards\Domain\Model\Keyforge\Tag\KeyforgeTagSynergyHigh;
use AdnanMula\Cards\Domain\Model\Keyforge\Tag\KeyforgeTagUpgradeCountHigh;
use AdnanMula\Cards\Domain\Service\Persistence\Fixture;
use AdnanMula\Cards\Infrastructure\Fixtures\DbalFixture;

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
        $this->save(new KeyforgeTagHasAgentZ());
        $this->save(new KeyforgeTagHasAnomaly());
        $this->save(new KeyforgeTagHasBoardWipes());
        $this->save(new KeyforgeTagHasDoubleCards());
        $this->save(new KeyforgeTagHasFangtoothCavern());
        $this->save(new KeyforgeTagHasHorseman());
        $this->save(new KeyforgeTagHasKeyCheats());
        $this->save(new KeyforgeTagHasLegacy());
        $this->save(new KeyforgeTagHasMaverick());
        $this->save(new KeyforgeTagHasRats());
        $this->save(new KeyforgeTagHasScalingAmberControl());
        $this->save(new KeyforgeTagHasSins());
        $this->save(new KeyforgeTagHasTimetraveller());
        $this->save(new KeyforgeTagOwnerBuko());
        $this->save(new KeyforgeTagOwnerDani());
        $this->save(new KeyforgeTagOwnerFran());
        $this->save(new KeyforgeTagOwnerIsmalelo());
        $this->save(new KeyforgeTagOwnerNan());
        $this->save(new KeyforgeTagOwnerNull());
        $this->save(new KeyforgeTagPercentile05());
        $this->save(new KeyforgeTagPercentile60());
        $this->save(new KeyforgeTagPercentile70());
        $this->save(new KeyforgeTagPercentile80());
        $this->save(new KeyforgeTagPercentile90());
        $this->save(new KeyforgeTagPercentile99());
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

    private function save(KeyforgeTag $tag): void
    {
        $stmt = $this->connection->prepare(
            \sprintf(
                '
                    INSERT INTO %s (id, name, visibility, style)
                    VALUES (:id, :name, :visibility, :style)
                    ON CONFLICT (id) DO UPDATE SET
                        id = :id,
                        name = :name,
                        visibility = :visibility,
                        style = :style
                    ',
                self::TABLE,
            ),
        );

        $stmt->bindValue(':id', $tag->id->value());
        $stmt->bindValue(':name', $tag->name());
        $stmt->bindValue(':visibility', $tag->visibility()->name);
        $stmt->bindValue(':style', Json::encode($tag->style()));

        $stmt->execute();
    }
}
