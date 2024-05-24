<?php declare(strict_types=1);

namespace AdnanMula\Cards\Domain\Model\Keyforge\Deck\ValueObject;

final readonly class KeyforgeCard implements \JsonSerializable
{
    private function __construct(
        public string $name,
        public string $serializedName,
        public KeyforgeCardRarity $rarity,
        public bool $isEnhanced,
        public bool $isMaverick,
        public bool $isLegacy,
        public bool $isAnomaly,
    ) {}

    public static function fromDokData(array $data): self
    {
        $serializedName = \strtolower($data['cardTitle']);
        $serializedName = \str_replace('-', '', $serializedName);
        $serializedName = \str_replace(' ', '-', $serializedName);
        $serializedName = \preg_replace('/[^A-Za-z0-9\-]/', '', $serializedName);

        return new self(
            $data['cardTitle'],
            $serializedName,
            KeyforgeCardRarity::from(\strtoupper($data['rarity'])),
            $data['enhanced'] ?? false,
            $data['maverick'] ?? false,
            $data['legacy'] ?? false,
            $data['anomaly'] ?? false,
        );
    }

    public function jsonSerialize(): array
    {
        return [
            'name' => $this->name,
            'serializedName' => $this->serializedName,
            'rarity' => $this->rarity->jsonSerialize(),
            'isEnhanced' => $this->isEnhanced,
            'isMaverick' => $this->isMaverick,
            'isLegacy' => $this->isLegacy,
            'isAnomaly' => $this->isAnomaly,
        ];
    }
}
