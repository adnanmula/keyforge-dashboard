<?php declare(strict_types=1);

namespace AdnanMula\Cards\Domain\Model\Keyforge\ValueObject;

use AdnanMula\Cards\Domain\Model\Shared\ValueObject\Uuid;

final class KeyforgeDeckStatHistory implements \JsonSerializable
{
    private function __construct(
        public readonly Uuid $deckId,
        public readonly int $dokDeckId,
        public readonly int $dokReference,
        public readonly int $sas,
        public readonly int $aercScore,
        public readonly int $aercVersion,
        public readonly float $expectedAmber,
        public readonly float $amberControl,
        public readonly float $creatureControl,
        public readonly float $artifactControl,
        public readonly float $efficiency,
        public readonly float $recursion,
        public readonly float $creatureProtection,
        public readonly float $disruption,
        public readonly float $other,
        public readonly float $effectivePower,
        public readonly int $synergyRating,
        public readonly int $antiSynergyRating,
        public readonly \DateTimeImmutable $updatedAt,
        public int $sasModified = 0,
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            deckId: Uuid::from($data['deck_id']),
            dokDeckId: $data['dok_deck_id'],
            dokReference: $data['dok_reference'],
            sas: (int) $data['sas'],
            aercScore: (int) $data['aerc_score'],
            aercVersion: (int) $data['aerc_version'],
            expectedAmber: (float) $data['expected_amber'],
            amberControl: (float) $data['amber_control'],
            creatureControl: (float) $data['creature_control'],
            artifactControl: (float) $data['artifact_control'],
            efficiency: (float) $data['efficiency'],
            recursion: (float) $data['recursion'],
            creatureProtection: (float) $data['creature_protection'],
            disruption: (float) $data['disruption'],
            other: (float) $data['other'],
            effectivePower: (float) $data['effective_power'],
            synergyRating: (int) $data['synergy_rating'],
            antiSynergyRating: (int) $data['antisynergy_rating'],
            updatedAt: new \DateTimeImmutable($data['updated_at']),
        );
    }

    public static function fromDokData(Uuid $deckId, array $data): self
    {
        return new self(
            deckId: $deckId,
            dokDeckId: $data['deckId'],
            dokReference: $data['id'],
            sas: $data['sasRating'],
            aercScore: $data['aercScore'],
            aercVersion: $data['aercVersion'],
            expectedAmber: $data['expectedAmber'],
            amberControl: $data['amberControl'],
            creatureControl: $data['creatureControl'],
            artifactControl: $data['artifactControl'],
            efficiency: $data['efficiency'],
            recursion: $data['recursion'],
            creatureProtection: $data['creatureProtection'],
            disruption: $data['disruption'],
            other: $data['other'],
            effectivePower: $data['effectivePower'],
            synergyRating: $data['synergyRating'],
            antiSynergyRating: $data['antisynergyRating'],
            updatedAt: new \DateTimeImmutable($data['updateDateTime']),
        );
    }

    public function setSasModified(int $value): self
    {
        $this->sasModified = $value;

        return $this;
    }

    public function jsonSerialize(): array
    {
        return [
            'deckId' => $this->deckId,
            'dokDeckId' => $this->dokDeckId,
            'dokReference' => $this->dokReference,
            'sas' => $this->sas,
            'sasModified' => $this->sasModified,
            'aercScore' => $this->aercScore,
            'aercVersion' => $this->aercVersion,
            'expectedAmber' => $this->expectedAmber,
            'creatureControl' => $this->creatureControl,
            'artifactControl' => $this->artifactControl,
            'efficiency' => $this->efficiency,
            'recursion' => $this->recursion,
            'creatureProtection' => $this->creatureProtection,
            'disruption' => $this->disruption,
            'other' => $this->other,
            'effectivePower' => $this->effectivePower,
            'synergyRating' => $this->synergyRating,
            'antiSynergyRating' => $this->antiSynergyRating,
            'lastSasUpdate' => $this->updatedAt->format('Y-m-d'),
        ];
    }
}
