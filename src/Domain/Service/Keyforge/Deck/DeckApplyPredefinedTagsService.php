<?php declare(strict_types=1);

namespace AdnanMula\Cards\Domain\Service\Keyforge\Deck;

use AdnanMula\Cards\Domain\Model\Keyforge\Deck\KeyforgeDeck;
use AdnanMula\Cards\Domain\Model\Keyforge\Deck\KeyforgeDeckRepository;
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
use AdnanMula\Cards\Domain\Model\Keyforge\Deck\ValueObject\KeyforgeDeckUserData;

final readonly class DeckApplyPredefinedTagsService
{
    public function __construct(
        private KeyforgeDeckRepository $repository,
    ) {}

    public function execute(KeyforgeDeck $deck): void
    {
        $newTags = [];

        $data = $deck->data()->rawData['deck'];

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
        $newTags[] = $this->tagHasAnomaly($anomalyCount);
        $newTags[] = $this->tagHasBoardWipes($data);
        $newTags[] = $this->tagHasKeyCheats($data);
        $newTags[] = $this->tagHasLegacy($legacyCount);
        $newTags[] = $this->tagHasMaverick($maverickCount);
        $newTags[] = $this->tagHasScalingAmberControl($data);
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

        $newTags = \array_filter($newTags);

        $this->repository->saveDeckUserData(
            KeyforgeDeckUserData::from(
                $deck->id(),
                $deck->userData()->owner,
                $deck->userData()->wins,
                $deck->userData()->losses,
                $deck->userData()->notes,
                $this->mergeTags($deck->userData()->tags, $newTags),
            ),
        );
    }

    private function mergeTags(array $currentTags, array $newTags): array
    {
        return \array_values(\array_unique(
            \array_merge($currentTags, \array_map(static fn (KeyforgeDeckTag $tag): string => $tag->id->value(), $newTags)),
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

    private function tagHasMaverick(int $maverick): ?KeyforgeDeckTag
    {
        if ($maverick > 0) {
            return new KeyforgeTagHasMaverick();
        }

        return null;
    }

    private function tagHasAnomaly(int $anomaly): ?KeyforgeDeckTag
    {
        if ($anomaly > 0) {
            return new KeyforgeTagHasAnomaly();
        }

        return null;
    }

    private function tagHasLegacy(int $legacy): ?KeyforgeDeckTag
    {
        if ($legacy > 0) {
            return new KeyforgeTagHasLegacy();
        }

        return null;
    }

    private function tagEfficiency(array $data): ?KeyforgeDeckTag
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

    private function tagArtifactCount(array $data): ?KeyforgeDeckTag
    {
        $artifacts = $data['artifactCount'] ?? 0;

        if ($artifacts >= 6) {
            return new KeyforgeTagArtifactCountHigh();
        }

        return null;
    }

    private function tagHasKeyCheats(array $data): ?KeyforgeDeckTag
    {
        $keyCheats = $data['keyCheatCount'] ?? 0;

        if ($keyCheats > 0) {
            return new KeyforgeTagHasKeyCheats();
        }

        return null;
    }

    private function tagEffectivePower(array $data): ?KeyforgeDeckTag
    {
        $effectivePower = $data['effectivePower'] ?? 0;

        if ($effectivePower > 110) {
            return new KeyforgeTagEffectivePowerHigh();
        }

        return null;
    }

    private function tagBonusAmber(array $data): ?KeyforgeDeckTag
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

    private function tagExpectedAmber(array $data): ?KeyforgeDeckTag
    {
        $expectedAmber = $data['expectedAmber'] ?? 0;

        if ($expectedAmber >= 25) {
            return new KeyforgeTagAmberExpectedHigh();
        }

        if ($expectedAmber <= 10) {
            return new KeyforgeTagAmberExpectedLow();
        }

        return null;
    }

    private function tagActionCount(array $data): ?KeyforgeDeckTag
    {
        $actions = $data['actionCount'] ?? 0;

        if ($actions >= 18) {
            return new KeyforgeTagActionCountHigh();
        }

        return null;
    }

    private function tagCreatureCount(array $data): ?KeyforgeDeckTag
    {
        $creatures = $data['creatureCount'] ?? 0;

        if ($creatures >= 22) {
            return new KeyforgeTagCreatureCountHigh();
        }

        return null;
    }

    private function tagArchiveCardCount(array $data): ?KeyforgeDeckTag
    {
        $value = $data['cardArchiveCount'] ?? 0;

        if ($value >= 6) {
            return new KeyforgeTagArchiveCardCountHigh();
        }

        return null;
    }

    private function tagUpgradeCount(array $data): ?KeyforgeDeckTag
    {
        $creatures = $data['upgradeCount'] ?? 0;

        if ($creatures >= 9) {
            return new KeyforgeTagUpgradeCountHigh();
        }

        return null;
    }

    private function tagAmberControl(array $data): ?KeyforgeDeckTag
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

    private function tagCreatureControl(array $data): ?KeyforgeDeckTag
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

    private function tagArtifactControl(array $data): ?KeyforgeDeckTag
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

    private function tagCreatureProtection(array $data): ?KeyforgeDeckTag
    {
        $value = $data['creatureProtection'] ?? 0;

        if ($value >= 6) {
            return new KeyforgeTagCreatureProtectionHigh();
        }

        return null;
    }

    private function tagRecursion(array $data): ?KeyforgeDeckTag
    {
        $value = $data['recursion'] ?? 0;

        if ($value >= 6) {
            return new KeyforgeTagRecursionHigh();
        }

        return null;
    }

    private function tagDisruption(array $data): ?KeyforgeDeckTag
    {
        $value = $data['disruption'] ?? 0;

        if ($value >= 9) {
            return new KeyforgeTagDisruptionHigh();
        }

        return null;
    }

    private function tagHasScalingAmberControl(array $data): ?KeyforgeDeckTag
    {
        $cards = [
            'Interdimensional Graft',
            'Doorstep to Heaven',
            'Bring Low',
            'Deusillus',
            'Ronnie Wristclocks',
            'Shatter Storm',
            'The First Scroll',
            'Rant and Rive',
            'Submersive Principle',
            'Martyr of the Vault',
            'Effervescent Principle',
            'ANT1-10NY',
            'Gatekeeper',
            'Trawler',
            'Cutthroat Research',
            'Too Much to Protect',
            'Burn the Stockpile',
            'Drumble',
            'Forgemaster Og',
            'Memorialize the Fallen',
            'Closed-Door Negotiation',
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

    private function tagHasBoardWipes(array $data): ?KeyforgeDeckTag
    {
        $cards = [
            'Tectonic Shift',
            'Phloxem Spike',
            'Opal Knight',
            'General Sherman',
            'Final Refrain',
            'Krrrzzzaaap!!!',
            'Guilty Hearts',
            'Onyx Knight',
            'Standardized Testing',
            'Three Fates',
            'Strange Gizmo',
            'General Order 24',
            'Earthshaker',
            'Axiom of Grisk',
            'Groundbreaking Discovery',
            'Selective Preservation',
            'Kiligog’s Trench',
            'Adult Swim',
            'Longfused Mines',
            'Market Crash',
            'Carpet Phloxem',
            'Crushing Charge',
            'Champion’s Challenge',
            'Bouncing Deathquark',
            'Neutron Shark',
            'The Spirit’s Way',
            'Mælstrom',
            'Echoing Deathknell',
            'Election',
            'Mass Buyout',
            'Concussive Transfer',
            'Unlocked Gateway',
            'Return to Rubble',
            'Ammonia Clouds',
            'Spartasaur',
            'Poison Wave',
            'Mind Over Matter',
            'Grand Alliance Council',
            'Hebe the Huge',
            'Gateway to Dis',
            'Mind Bullets',
            'Hysteria',
            'Ballcano',
            'Coward’s End',
            'Phoenix Heart',
            'Infighting',
            'Dark Wave',
            'Tendrils of Pain',
            'Piranha Monkeys',
            'Harbinger of Doom',
            'Skixuno',
            'Numquid the Fair',
            'Key to Dis',
            'Final Analysis',
            'Plan 10',
            'Plummet',
            'Midyear Festivities',
            'Kaboom!',
            'Unnatural Selection',
            'Plague Wind',
            'Æmberlution',
            'Savage Clash',
            'Winds of Death',
            'De-escalation',
            'War of the Worlds',
            'Ragnarok',
            'Catch and Release',
            'Soul Bomb',
            'Into the Warp',
            'The Big One',
            'Harvest Time',
            'Extinction',
            'Dance of Doom',
            'Tertiate',
            'Quintrino Warp',
            'Gleeful Mayhem',
            'Quintrino Flux',
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

    private function tagSynergy(array $data): ?KeyforgeDeckTag
    {
        $value = $data['synergyRating'] ?? 0;

        if ($value >= 15) {
            return new KeyforgeTagSynergyHigh();
        }

        return null;
    }

    private function tagAntiSynergy(array $data): ?KeyforgeDeckTag
    {
        $value = $data['antisynergyRating'] ?? 0;

        if ($value >= 2) {
            return new KeyforgeTagAntiSynergyHigh();
        }

        return null;
    }
}
