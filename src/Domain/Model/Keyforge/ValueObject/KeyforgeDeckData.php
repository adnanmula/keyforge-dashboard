<?php declare(strict_types=1);

namespace AdnanMula\Cards\Domain\Model\Keyforge\ValueObject;

final class KeyforgeDeckData implements \JsonSerializable
{
    private function __construct(
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
    ) {}

    public static function fromDokData(array $data): self
    {
        return new self(
            $data['deck']['amberControl'] ?? 0,
            $data['deck']['artifactControl'] ?? 0,
            $data['deck']['expectedAmber'] ?? 0,
            $data['deck']['creatureControl'] ?? 0,
            $data['deck']['efficiency'] ?? 0,
            $data['deck']['recursion'] ?? 0,
            $data['deck']['disruption'] ?? 0,
            $data['deck']['effectivePower'] ?? 0,
            $data['deck']['creatureProtection'] ?? 0,
            $data['deck']['other'] ?? 0,
        );
    }

    public function jsonSerialize(): array
    {
        return [
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
        ];
    }
}
