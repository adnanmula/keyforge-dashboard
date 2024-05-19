<?php declare(strict_types=1);

namespace AdnanMula\Cards\Domain\Model\Keyforge\ValueObject;

final readonly class KeyforgeDeckStats implements \JsonSerializable
{
    private function __construct(
        public int $sas,
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
        public int $synergyRating,
        public int $antiSynergyRating,
        public int $aercScore,
        public int $aercVersion,
        public int $sasVersion,
        public float $sasPercentile,
        public int $previousSasRating,
        public int $previousMajorSasRating,
        public \DateTimeImmutable $lastSasUpdate,
    ) {}

    public static function fromDokData(array $data): self
    {
        $deck = $data['deck'];

        return new self(
            sas: $deck['sasRating'] ?? 0,
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
            synergyRating: $deck['synergyRating'] ?? 0,
            antiSynergyRating: $deck['antisynergyRating'] ?? 0,
            aercScore: $deck['aercScore'] ?? 0,
            aercVersion: $deck['aercVersion'] ?? 0,
            sasVersion: $data['sasVersion'] ?? 0,
            sasPercentile: $deck['sasPercentile'] ?? 0,
            previousSasRating: $deck['previousSasRating'] ?? 0,
            previousMajorSasRating: $deck['previousMajorSasRating'] ?? 0,
            lastSasUpdate: new \DateTimeImmutable($deck['lastSasUpdate']),
        );
    }

    public function jsonSerialize(): array
    {
        return [
            'sas' => $this->sas,
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
            'synergyRating' => $this->synergyRating,
            'antiSynergyRating' => $this->antiSynergyRating,
            'aercScore' => $this->aercScore,
            'aercVersion' => $this->aercVersion,
            'sasVersion' => $this->sasVersion,
            'sasPercentile' => \round($this->sasPercentile, 2),
            'previousSasRating' => $this->previousSasRating,
            'previousMajorSasRating' => $this->previousMajorSasRating,
            'lastSasUpdate' => $this->lastSasUpdate->format('Y-m-d'),
        ];
    }
}
