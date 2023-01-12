<?php declare(strict_types=1);

namespace AdnanMula\Cards\Domain\Service\Keyforge\Deck;

use AdnanMula\Cards\Domain\Model\Keyforge\KeyforgeDeck;
use AdnanMula\Cards\Domain\Model\Keyforge\KeyforgeDeckRepository;
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
use AdnanMula\Cards\Domain\Model\Keyforge\Tag\KeyforgeTagHasBRIG;
use AdnanMula\Cards\Domain\Model\Keyforge\Tag\KeyforgeTagHasCollectorWorm;
use AdnanMula\Cards\Domain\Model\Keyforge\Tag\KeyforgeTagHasDoubleCards;
use AdnanMula\Cards\Domain\Model\Keyforge\Tag\KeyforgeTagHasDysania;
use AdnanMula\Cards\Domain\Model\Keyforge\Tag\KeyforgeTagHasFangtoothCavern;
use AdnanMula\Cards\Domain\Model\Keyforge\Tag\KeyforgeTagHasGenKa;
use AdnanMula\Cards\Domain\Model\Keyforge\Tag\KeyforgeTagHasHeartOfTheForest;
use AdnanMula\Cards\Domain\Model\Keyforge\Tag\KeyforgeTagHasHorseman;
use AdnanMula\Cards\Domain\Model\Keyforge\Tag\KeyforgeTagHasKeyCheats;
use AdnanMula\Cards\Domain\Model\Keyforge\Tag\KeyforgeTagHasLegacy;
use AdnanMula\Cards\Domain\Model\Keyforge\Tag\KeyforgeTagHasMaverick;
use AdnanMula\Cards\Domain\Model\Keyforge\Tag\KeyforgeTagHasOrtannu;
use AdnanMula\Cards\Domain\Model\Keyforge\Tag\KeyforgeTagHasRats;
use AdnanMula\Cards\Domain\Model\Keyforge\Tag\KeyforgeTagHasScalingAmberControl;
use AdnanMula\Cards\Domain\Model\Keyforge\Tag\KeyforgeTagHasSins;
use AdnanMula\Cards\Domain\Model\Keyforge\Tag\KeyforgeTagHasTimetraveller;
use AdnanMula\Cards\Domain\Model\Keyforge\Tag\KeyforgeTagHasXenos;
use AdnanMula\Cards\Domain\Model\Keyforge\Tag\KeyforgeTagOwnerBuko;
use AdnanMula\Cards\Domain\Model\Keyforge\Tag\KeyforgeTagOwnerChopi;
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

final class DeckApplyPredefinedTagsService
{
    public function __construct(
        private readonly KeyforgeDeckRepository $repository,
    ) {}

    public function execute(KeyforgeDeck $deck): void
    {
        $newTags = [];

        /** @var array $data */
        $data = $deck->extraData()['deck'];

        [$maverickCount, $legacyCount, $anomalyCount] = $this->specialCardsCount($data);

        $newTags[] = $this->tagActionCount($data);
        $newTags[] = $this->tagAmberControl($data);
        $newTags[] = $this->tagAntiSynergy($data);
        $newTags[] = $this->tagArchiveCardCount($data);
        $newTags[] = $this->tagArtifactControl($data);
        $newTags[] = $this->tagArtifactCount($data);
        $newTags[] = $this->tagBonusAmber($data);
        $newTags[] = $this->tagCreatureControl($data);
        $newTags[] = $this->tagCreatureCount($data);
        $newTags[] = $this->tagCreatureProtection($data);
        $newTags[] = $this->tagDisruption($data);
        $newTags[] = $this->tagEffectivePower($data);
        $newTags[] = $this->tagEfficiency($data);
        $newTags[] = $this->tagExpectedAmber($data);
        $newTags[] = $this->tagHasAgentZ($data);
        $newTags[] = $this->tagHasAnomaly($anomalyCount);
        $newTags[] = $this->tagHasBRIG($data);
        $newTags[] = $this->tagHasBoardWipes($data);
        $newTags[] = $this->tagHasCavern($data);
        $newTags[] = $this->tagHasCollectorWorm($data);
        $newTags[] = $this->tagHasDoubleCards($data);
        $newTags[] = $this->tagHasDysania($data);
        $newTags[] = $this->tagHasGenKa($data);
        $newTags[] = $this->tagHasHeartOfTheForest($data);
        $newTags[] = $this->tagHasHorseman($data);
        $newTags[] = $this->tagHasKeyCheats($data);
        $newTags[] = $this->tagHasLegacy($legacyCount);
        $newTags[] = $this->tagHasMaverick($maverickCount);
        $newTags[] = $this->tagHasOrtannu($data);
        $newTags[] = $this->tagHasRats($data);
        $newTags[] = $this->tagHasScalingAmberControl($data);
        $newTags[] = $this->tagHasSins($data);
        $newTags[] = $this->tagHasTimetraveller($data);
        $newTags[] = $this->tagHasXenos($data);
        $newTags[] = $this->tagPercentiles($data);
        $newTags[] = $this->tagRecursion($data);
        $newTags[] = $this->tagSynergy($data);
        $newTags[] = $this->tagUpgradeCount($data);

        $draftDecks = [
            '19ee9a3b-cbe5-4fe5-b4a5-388a1cc3c37a',
            '37259b93-1cdd-4ea8-8206-767b071b2643',
            'eaa1eb19-6ec9-400f-8881-b88eeddd06bc',
            'dcbc4eae-b03b-4a75-a8ba-65742f1ca1c6',
        ];

        if (\in_array($deck->id()->value(), $draftDecks, true)) {
            $newTags = [];
        }

        $newTags[] = $this->tagOwner($deck);

        $newTags = \array_filter($newTags);

        $deck->updateTags(...$this->mergeTags($deck->tags(), $newTags));

        $this->repository->save($deck);
    }

    private function mergeTags(array $currentTags, array $newTags): array
    {
        return \array_values(\array_unique(
            \array_merge($currentTags, \array_map(static fn (KeyforgeTag $tag): string => $tag->id->value(), $newTags)),
        ));
    }

    private function specialCardsCount(array $data): array
    {
        $maverickCount = 0;
        $legacyCount = 0;
        $anomalyCount = 0;

        foreach ($data['housesAndCards'] as $house) {
            foreach ($house['cards'] as $card) {
                if ($card['legacy']) {
                    $legacyCount++;
                }

                if ($card['maverick']) {
                    $maverickCount++;
                }

                if ($card['anomaly']) {
                    $anomalyCount++;
                }
            }
        }

        return [$maverickCount, $legacyCount, $anomalyCount];
    }

    private function tagPercentiles(array $data): ?KeyforgeTag
    {
        $sasPercentile = $data['sasPercentile'];

        if ($sasPercentile <= 5) {
            return new KeyforgeTagPercentile05();
        }

        if ($sasPercentile >= 60 && $sasPercentile < 70) {
            return new KeyforgeTagPercentile60();
        }

        if ($sasPercentile >= 70 && $sasPercentile < 80) {
            return new KeyforgeTagPercentile70();
        }

        if ($sasPercentile >= 80 && $sasPercentile < 90) {
            return new KeyforgeTagPercentile80();
        }

        if ($sasPercentile >= 90 && $sasPercentile < 99) {
            return new KeyforgeTagPercentile90();
        }

        if ($sasPercentile >= 99) {
            return new KeyforgeTagPercentile99();
        }

        return null;
    }

    private function tagHasMaverick(int $maverick): ?KeyforgeTag
    {
        if ($maverick > 0) {
            return new KeyforgeTagHasMaverick();
        }

        return null;
    }

    private function tagHasAnomaly(int $anomaly): ?KeyforgeTag
    {
        if ($anomaly > 0) {
            return new KeyforgeTagHasAnomaly();
        }

        return null;
    }

    private function tagHasLegacy(int $legacy): ?KeyforgeTag
    {
        if ($legacy > 0) {
            return new KeyforgeTagHasLegacy();
        }

        return null;
    }

    private function tagEfficiency(array $data): ?KeyforgeTag
    {
        $efficiency = $data['efficiency'] ?? 0;

        if ($efficiency >= 15) {
            return new KeyforgeTagEfficiencyHigh();
        }

        if ($efficiency <= -1) {
            return new KeyforgeTagEfficiencyLow();
        }

        return null;
    }

    private function tagArtifactCount(array $data): ?KeyforgeTag
    {
        $artifacts = $data['artifactCount'] ?? 0;

        if ($artifacts >= 6) {
            return new KeyforgeTagArtifactCountHigh();
        }

        return null;
    }

    private function tagHasKeyCheats(array $data): ?KeyforgeTag
    {
        $keyCheats = $data['keyCheatCount'] ?? 0;

        if ($keyCheats > 0) {
            return new KeyforgeTagHasKeyCheats();
        }

        return null;
    }

    private function tagEffectivePower(array $data): ?KeyforgeTag
    {
        $effectivePower = $data['effectivePower'] ?? 0;

        if ($effectivePower > 110) {
            return new KeyforgeTagEffectivePowerHigh();
        }

        return null;
    }

    private function tagBonusAmber(array $data): ?KeyforgeTag
    {
        $bonusAmber = $data['rawAmber'] ?? 0;

        if ($bonusAmber >= 15) {
            return new KeyforgeTagAmberBonusHigh();
        }

        if ($bonusAmber <= 5) {
            return new KeyforgeTagAmberBonusLow();
        }

        return null;
    }

    private function tagExpectedAmber(array $data): ?KeyforgeTag
    {
        $expectedAmber = $data['expectedAmber'] ?? 0;

        if ($expectedAmber >= 25) {
            return new KeyforgeTagAmberExpectedHigh();
        }

        if ($expectedAmber <= 5) {
            return new KeyforgeTagAmberExpectedLow();
        }

        return null;
    }

    private function tagActionCount(array $data): ?KeyforgeTag
    {
        $actions = $data['actionCount'] ?? 0;

        if ($actions >= 18) {
            return new KeyforgeTagActionCountHigh();
        }

        return null;
    }

    private function tagCreatureCount(array $data): ?KeyforgeTag
    {
        $creatures = $data['creatureCount'] ?? 0;

        if ($creatures >= 22) {
            return new KeyforgeTagCreatureCountHigh();
        }

        return null;
    }

    private function tagArchiveCardCount(array $data): ?KeyforgeTag
    {
        $value = $data['cardArchiveCount'] ?? 0;

        if ($value >= 6) {
            return new KeyforgeTagArchiveCardCountHigh();
        }

        return null;
    }

    private function tagUpgradeCount(array $data): ?KeyforgeTag
    {
        $creatures = $data['upgradeCount'] ?? 0;

        if ($creatures >= 9) {
            return new KeyforgeTagUpgradeCountHigh();
        }

        return null;
    }

    private function tagAmberControl(array $data): ?KeyforgeTag
    {
        $value = $data['amberControl'] ?? 0;

        if ($value >= 15) {
            return new KeyforgeTagAmberControlHigh();
        }

        if ($value < 2) {
            return new KeyforgeTagAmberControlLow();
        }

        return null;
    }

    private function tagCreatureControl(array $data): ?KeyforgeTag
    {
        $value = $data['creatureControl'] ?? 0;

        if ($value >= 15) {
            return new KeyforgeTagCreatureControlHigh();
        }

        if ($value < 4) {
            return new KeyforgeTagCreatureControlLow();
        }

        return null;
    }

    private function tagArtifactControl(array $data): ?KeyforgeTag
    {
        $value = $data['artifactControl'] ?? 0;

        if ($value >= 2) {
            return new KeyforgeTagArtifactControlHigh();
        }

        if ($value === 0) {
            return new KeyforgeTagArtifactControlLow();
        }

        return null;
    }

    private function tagCreatureProtection(array $data): ?KeyforgeTag
    {
        $value = $data['creatureProtection'] ?? 0;

        if ($value >= 6) {
            return new KeyforgeTagCreatureProtectionHigh();
        }

        return null;
    }

    private function tagRecursion(array $data): ?KeyforgeTag
    {
        $value = $data['recursion'] ?? 0;

        if ($value >= 6) {
            return new KeyforgeTagRecursionHigh();
        }

        return null;
    }

    private function tagDisruption(array $data): ?KeyforgeTag
    {
        $value = $data['disruption'] ?? 0;

        if ($value >= 9) {
            return new KeyforgeTagDisruptionHigh();
        }

        return null;
    }

    private function tagHasScalingAmberControl(array $data): ?KeyforgeTag
    {
        $cards = [
            'Burn the Stockpile',
            'Effervescent Principle',
            'Too Much to Protect',
            'Interdimensional Graft',
            'Shatter Storm',
            'Doorstep to Heaven',
            'Deusillus',
        ];

        $count = 0;

        foreach ($data['housesAndCards'] as $house) {
            foreach ($house['cards'] as $card) {
                if (\in_array($card['cardTitle'], $cards, true)) {
                    $count++;
                }
            }
        }

        if ($count > 0) {
            return new KeyforgeTagHasScalingAmberControl();
        }

        return null;
    }

    private function tagHasBoardWipes(array $data): ?KeyforgeTag
    {
        $cards = [
            'Ballcano',
            'Axiom of Grist',
            'Spartasaur',
            'Key to Dis',
            'Harbinger of Doom',
            'Ragnarok',
            'Unnatural Selection',
            'Strange Gizmo',
            'Red Alert',
            'Champion’s Challenge',
            'Unlocked Gateway',
            'Coward’s End',
            'Earthshaker',
            'The Big One',
            'Gateway to Dis',
            'Savage Clash',
            'Skixuno',
            'Onyx Knight',
            'Groundbreaking Discovery',
            'Krrrzzzaaap',
            'Ammonia Clouds',
            'Numquid the Fair',
            'The Spirit’s Way',
            'Opal Knight',
            'Crushing Charge',
            'Mind Bullets',
            'Grand Alliance Council',
            'General Sherman',
            "Kiligog's Trench",
            'Mælstrom',
            'Æmberlution',
        ];

        $count = 0;

        foreach ($data['housesAndCards'] as $house) {
            foreach ($house['cards'] as $card) {
                if (\in_array($card['cardTitle'], $cards, true)) {
                    $count++;
                }
            }
        }

        if ($count > 0) {
            return new KeyforgeTagHasBoardWipes();
        }

        return null;
    }

    private function tagSynergy(array $data): ?KeyforgeTag
    {
        $value = $data['synergyRating'] ?? 0;

        if ($value >= 15) {
            return new KeyforgeTagSynergyHigh();
        }

        return null;
    }

    private function tagAntiSynergy(array $data): ?KeyforgeTag
    {
        $value = $data['antisynergyRating'] ?? 0;

        if ($value >= 2) {
            return new KeyforgeTagAntiSynergyHigh();
        }

        return null;
    }

    private function tagHasCavern(array $data): ?KeyforgeTag
    {
        $cards = [
            'Fangtooth Cavern',
        ];

        if ($this->hasCard($cards, $data)) {
            return new KeyforgeTagHasFangtoothCavern();
        }

        return null;
    }

    private function tagHasAgentZ(array $data): ?KeyforgeTag
    {
        $cards = [
            'Z-Force Agent 14',
        ];

        if ($this->hasCard($cards, $data)) {
            return new KeyforgeTagHasAgentZ();
        }

        return null;
    }

    private function tagHasTimetraveller(array $data): ?KeyforgeTag
    {
        $cards = [
            'Timetraveller',
        ];

        if ($this->hasCard($cards, $data)) {
            return new KeyforgeTagHasTimetraveller();
        }

        return null;
    }

    private function tagHasRats(array $data): ?KeyforgeTag
    {
        $cards = [
            'Plague Rat',
        ];

        if ($this->hasCard($cards, $data)) {
            return new KeyforgeTagHasRats();
        }

        return null;
    }

    private function tagHasDoubleCards(array $data): ?KeyforgeTag
    {
        $cards = [
            'Deusillus',
            'Ultra Gravitron',
            'Niffle Kong',
        ];

        if ($this->hasCard($cards, $data)) {
            return new KeyforgeTagHasDoubleCards();
        }

        return null;
    }

    private function tagHasHorseman(array $data): ?KeyforgeTag
    {
        $cards = [
            'Horseman of Death',
            'Horseman of Famine',
            'Horseman of Pestilence',
            'Horseman of War',
        ];

        if ($this->hasCard($cards, $data)) {
            return new KeyforgeTagHasHorseman();
        }

        return null;
    }

    private function tagHasSins(array $data): ?KeyforgeTag
    {
        $cards = [
            'Gluttony',
            'Envy',
            'Desire',
            'Greed',
            'Pride',
            'Sloth',
            'Wrath',
        ];

        if ($this->hasCard($cards, $data)) {
            return new KeyforgeTagHasSins();
        }

        return null;
    }

    private function tagHasDysania(array $data): ?KeyforgeTag
    {
        $cards = ['Dysania'];

        if ($this->hasCard($cards, $data)) {
            return new KeyforgeTagHasDysania();
        }

        return null;
    }

    private function tagHasOrtannu(array $data): ?KeyforgeTag
    {
        $cards = ['Ortannu the Chained'];

        if ($this->hasCard($cards, $data)) {
            return new KeyforgeTagHasOrtannu();
        }

        return null;
    }

    private function tagHasCollectorWorm(array $data): ?KeyforgeTag
    {
        $cards = ['Collector Worm'];

        if ($this->hasCard($cards, $data)) {
            return new KeyforgeTagHasCollectorWorm();
        }

        return null;
    }

    private function tagHasGenKa(array $data): ?KeyforgeTag
    {
        if ($this->hasCard(['Martian Generosity'], $data) && $this->hasCard(['Key Abduction'], $data)) {
            return new KeyforgeTagHasGenKa();
        }

        return null;
    }

    private function tagHasBRIG(array $data): ?KeyforgeTag
    {
        if ($this->hasCard(['Binate Rupture'], $data) && $this->hasCard(['Interdimensional Graft'], $data)) {
            return new KeyforgeTagHasBRIG();
        }

        return null;
    }

    private function tagHasHeartOfTheForest(array $data): ?KeyforgeTag
    {
        $cards = ['Heart of the Forest'];

        if ($this->hasCard($cards, $data)) {
            return new KeyforgeTagHasHeartOfTheForest();
        }

        return null;
    }

    private function tagHasXenos(array $data): ?KeyforgeTag
    {
        $cards = ['Xenos Bloodshadow'];

        if ($this->hasCard($cards, $data)) {
            return new KeyforgeTagHasXenos();
        }

        return null;
    }

    private function hasCard(array $cards, array $data): bool
    {
        foreach ($data['housesAndCards'] as $house) {
            foreach ($house['cards'] as $card) {
                if (\in_array($card['cardTitle'], $cards, true)) {
                    return true;
                }
            }
        }

        return false;
    }

    private function tagOwner(KeyforgeDeck $deck): ?KeyforgeTag
    {
        $owner = $deck->owner()?->value();

        if (null === $owner) {
            return new KeyforgeTagOwnerNull();
        }

        if ($owner === '4ec5768e-f09f-4c9c-ab94-ff5bcb3e38c1') {
            return new KeyforgeTagOwnerNan();
        }

        if ($owner === 'dd055fa6-cafd-4356-b283-54ce606f86b2') {
            return new KeyforgeTagOwnerBuko();
        }

        if ($owner === 'f752ba28-e975-4ae8-869f-f56e02e67922') {
            return new KeyforgeTagOwnerIsmalelo();
        }

        if ($owner === 'c98fd22a-c355-4fb9-88c9-2a2123a43321') {
            return new KeyforgeTagOwnerFran();
        }

        if ($owner === 'b86488f4-336e-4c23-a34d-e92f06b51c04') {
            return new KeyforgeTagOwnerDani();
        }

        if ($owner === 'f6f51a9f-28d1-416c-aeb0-28c5d8c23d9c') {
            return new KeyforgeTagOwnerChopi();
        }

        return null;
    }
}
