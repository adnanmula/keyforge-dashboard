<?php declare(strict_types=1);

namespace AdnanMula\Cards\Domain\Model\Keyforge\ValueObject;

use AdnanMula\Cards\Domain\Model\Shared\ValueObject\Uuid;

final readonly class KeyforgeDeckStatHistory implements \JsonSerializable
{
    private function __construct(
        public Uuid $deckId,
        public int $dokDeckId,
        public int $dokReference,
        public int $sas,
        public int $aercScore,
        public int $aercVersion,
        public float $expectedAmber,
        public float $creatureControl,
        public float $artifactControl,
        public float $efficiency,
        public float $recursion,
        public float $creatureProtection,
        public float $disruption,
        public float $other,
        public float $effectivePower,
        public int $synergyRating,
        public int $antiSynergyRating,
        public \DateTimeImmutable $updatedAt,
    ) {}

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

    public function jsonSerialize(): array
    {
        return [
            'deckId' => $this->deckId,
            'dokDeckId' => $this->dokDeckId,
            'dokReference' => $this->dokReference,
            'sas' => $this->sas,
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
