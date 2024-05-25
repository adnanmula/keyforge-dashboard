<?php declare(strict_types=1);

namespace AdnanMula\Cards\Domain\Model\Keyforge\Deck\ValueObject;

final readonly class KeyforgeDeckStats implements \JsonSerializable
{
    private function __construct(
        public int $sas,
        public int $previousSasRating,
        public int $previousMajorSasRating,
        public float $sasPercentile,
        public int $sasVersion,
        public int $aercScore,
        public int $aercVersion,
        public float $amberControl,
        public float $artifactControl,
        public float $expectedAmber,
        public float $creatureControl,
        public float $efficiency,
        public float $recursion,
        public float $disruption,
        public float $effectivePower,
        public float $creatureProtection,
        public float $other,
        public int $rawAmber,
        public int $totalPower,
        public int $totalArmor,
        public float $efficiencyBonus,
        public int $creatureCount,
        public int $actionCount,
        public int $artifactCount,
        public int $upgradeCount,
        public int $cardDrawCount,
        public int $cardArchiveCount,
        public int $keyCheatCount,
        public int $boardClearCount,
        public int $scalingAmberControlCount,
        public int $synergyRating,
        public int $antiSynergyRating,
        public \DateTimeImmutable $lastSasUpdate,
    ) {}

    public static function fromDokData(array $data): self
    {
        $deck = $data['deck'];

        // @codingStandardsIgnoreStart
        $boardClears = ['tectonic-shift', 'phloxem-spike', 'opal-knight', 'general-sherman', 'final-refrain', 'krrrzzzaaap', 'guilty-hearts', 'onyx-knight', 'standardized-testing', 'three-fates', 'strange-gizmo', 'general-order-24', 'earthshaker', 'axiom-of-grisk', 'groundbreaking-discovery', 'selective-preservation', 'kiligogs-trench', 'adult-swim', 'longfused-mines', 'market-crash', 'carpet-phloxem', 'crushing-charge', 'champions-challenge', 'bouncing-deathquark', 'neutron-shark', 'the-spirits-way', 'mlstrom', 'echoing-deathknell', 'election', 'mass-buyout', 'concussive-transfer', 'unlocked-gateway', 'return-to-rubble', 'ammonia-clouds', 'spartasaur', 'poison-wave', 'mind-over-matter', 'grand-alliance-council', 'hebe-the-huge', 'gateway-to-dis', 'mind-bullets', 'hysteria', 'ballcano', 'cowards-end', 'phoenix-heart', 'infighting', 'dark-wave', 'tendrils-of-pain', 'piranha-monkeys', 'harbinger-of-doom', 'skixuno', 'numquid-the-fair', 'key-to-dis', 'final-analysis', 'plan-10', 'plummet', 'midyear-festivities', 'kaboom', 'unnatural-selection', 'plague-wind', 'mberlution', 'savage-clash', 'winds-of-death', 'deescalation', 'war-of-the-worlds', 'ragnarok', 'catch-and-release', 'soul-bomb', 'into-the-warp', 'the-big-one', 'harvest-time', 'extinction', 'dance-of-doom', 'tertiate', 'quintrino-warp', 'gleeful-mayhem', 'quintrino-flux'];
        $scaling = ['interdimensional-graft', 'doorstep-to-heaven', 'bring-low', 'deusillus', 'ronnie-wristclocks', 'shatter-storm', 'the-first-scroll', 'rant-and-rive', 'submersive-principle', 'martyr-of-the-vault', 'effervescent-principle', 'ant110ny', 'gatekeeper', 'trawler', 'cutthroat-research', 'too-much-to-protect', 'burn-the-stockpile', 'drumble', 'forgemaster-og', 'memorialize-the-fallen', 'closeddoor-negotiation'];
        // @codingStandardsIgnoreEnd

        $boardClearCount = 0;
        $scalingAmberControlCount = 0;

        foreach ($data['deck']['housesAndCards'] as $houseCards) {
            foreach ($houseCards['cards'] as $card) {
                if (\in_array($card['cardTitleUrl'], $boardClears, true)) {
                    $boardClearCount++;
                }

                if (\in_array($card['cardTitleUrl'], $scaling, true)) {
                    $scalingAmberControlCount++;
                }
            }
        }

        return new self(
            sas: $deck['sasRating'] ?? 0,
            previousSasRating: $deck['previousSasRating'] ?? 0,
            previousMajorSasRating: $deck['previousMajorSasRating'] ?? 0,
            sasPercentile: $deck['sasPercentile'] ?? 0,
            sasVersion: $data['sasVersion'] ?? 0,
            aercScore: $deck['aercScore'] ?? 0,
            aercVersion: $deck['aercVersion'] ?? 0,
            amberControl: $deck['amberControl'] ?? 0,
            artifactControl: $deck['artifactControl'] ?? 0,
            expectedAmber: $deck['expectedAmber'] ?? 0,
            creatureControl: $deck['creatureControl'] ?? 0,
            efficiency: $deck['efficiency'] ?? 0,
            recursion: $deck['recursion'] ?? 0,
            disruption: $deck['disruption'] ?? 0,
            effectivePower: $deck['effectivePower'] ?? 0,
            creatureProtection: $deck['creatureProtection'] ?? 0,
            other: $deck['other'] ?? 0,
            rawAmber: $deck['rawAmber'] ?? 0,
            totalPower: $deck['totalPower'] ?? 0,
            totalArmor: $deck['totalArmor'] ?? 0,
            efficiencyBonus: $deck['efficiencyBonus'] ?? 0,
            creatureCount: $deck['creatureCount'] ?? 0,
            actionCount: $deck['actionCount'] ?? 0,
            artifactCount: $deck['artifactCount'] ?? 0,
            upgradeCount: $deck['upgradeCount'] ?? 0,
            cardDrawCount: $deck['cardDrawCount'] ?? 0,
            cardArchiveCount: $deck['cardArchiveCount'] ?? 0,
            keyCheatCount: $deck['keyCheatCount'] ?? 0,
            boardClearCount: $boardClearCount,
            scalingAmberControlCount: $scalingAmberControlCount,
            synergyRating: $deck['synergyRating'] ?? 0,
            antiSynergyRating: $deck['antisynergyRating'] ?? 0,
            lastSasUpdate: new \DateTimeImmutable($deck['lastSasUpdate']),
        );
    }

    public function jsonSerialize(): array
    {
        return [
            'sas' => $this->sas,
            'previousSasRating' => $this->previousSasRating,
            'previousMajorSasRating' => $this->previousMajorSasRating,
            'sasPercentile' => \round($this->sasPercentile, 2),
            'sasVersion' => $this->sasVersion,
            'aercScore' => $this->aercScore,
            'aercVersion' => $this->aercVersion,
            'amberControl' => \round($this->amberControl, 2),
            'artifactControl' => \round($this->artifactControl, 2),
            'expectedAmber' => \round($this->expectedAmber, 2),
            'creatureControl' => \round($this->creatureControl, 2),
            'efficiency' => \round($this->efficiency, 2),
            'recursion' => \round($this->recursion, 2),
            'disruption' => \round($this->disruption, 2),
            'effectivePower' => \round($this->effectivePower, 2),
            'creatureProtection' => \round($this->creatureProtection, 2),
            'other' => \round($this->other, 2),
            'rawAmber' => $this->rawAmber,
            'totalPower' => $this->totalPower,
            'totalArmor' => $this->totalArmor,
            'efficiencyBonus' => \round($this->efficiencyBonus, 2),
            'creatureCount' => $this->creatureCount,
            'actionCount' => $this->actionCount,
            'artifactCount' => $this->artifactCount,
            'upgradeCount' => $this->upgradeCount,
            'cardDrawCount' => $this->cardDrawCount,
            'cardArchiveCount' => $this->cardArchiveCount,
            'keyCheatCount' => $this->keyCheatCount,
            'boardClearCount' => $this->boardClearCount,
            'scalingAmberControlCount' => $this->scalingAmberControlCount,
            'synergyRating' => $this->synergyRating,
            'antiSynergyRating' => $this->antiSynergyRating,
            'lastSasUpdate' => $this->lastSasUpdate->format('Y-m-d'),
        ];
    }
}
