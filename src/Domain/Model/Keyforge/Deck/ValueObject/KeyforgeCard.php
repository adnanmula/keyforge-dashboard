<?php declare(strict_types=1);

namespace AdnanMula\Cards\Domain\Model\Keyforge\Deck\ValueObject;

final readonly class KeyforgeCard implements \JsonSerializable
{
    private function __construct(
        public string $name,
        public string $serializedName,
        public ?string $imageUrl,
        public KeyforgeCardRarity $rarity,
        public bool $isEnhanced,
        public bool $isMaverick,
        public bool $isLegacy,
        public bool $isAnomaly,
        public int $bonusAember,
        public int $bonusCapture,
        public int $bonusDamage,
        public int $bonusDraw,
        public int $bonusDiscard,
        public int $bonusPower,
        public array $bonusHouses = [],
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            $data['name'],
            $data['serializedName'],
            $data['imageUrl'] ?? '',
            KeyforgeCardRarity::from(\strtoupper($data['rarity'])),
            $data['isEnhanced'] ?? false,
            $data['isMaverick'] ?? false,
            $data['isLegacy'] ?? false,
            $data['isAnomaly'] ?? false,
            $data['bonusAember'] ?? 0,
            $data['bonusCapture'] ?? 0,
            $data['bonusDamage'] ?? 0,
            $data['bonusDraw'] ?? 0,
            $data['bonusDiscard'] ?? 0,
            $data['bonusPower'] ?? 0,
            $data['bonusHouses'] ?? [],
        );
    }

    public static function fromDokData(array $data): self
    {
        $urlPieces = explode('/', $data['cardTitleUrl']);
        $serializedName = explode('.', end($urlPieces))[0];

        return new self(
            $data['cardTitle'],
            $serializedName,
            $data['cardTitleUrl'],
            KeyforgeCardRarity::from(\strtoupper($data['rarity'])),
            $data['enhanced'] ?? false,
            $data['maverick'] ?? false,
            $data['legacy'] ?? false,
            $data['anomaly'] ?? false,
            $data['bonusAember'] ?? 0,
            $data['bonusCapture'] ?? 0,
            $data['bonusDamage'] ?? 0,
            $data['bonusDraw'] ?? 0,
            $data['bonusDiscard'] ?? 0,
            $data['bonusPower'] ?? 0,
            array_map(static fn (string $h) => KeyforgeHouse::fromDokName($h)->value, $data['bonusHouses'] ?? []),
        );
    }

    public function jsonSerialize(): array
    {
        return [
            'name' => $this->name,
            'serializedName' => $this->serializedName,
            'imageUrl' => $this->imageUrl,
            'rarity' => $this->rarity->jsonSerialize(),
            'isEnhanced' => $this->isEnhanced,
            'isMaverick' => $this->isMaverick,
            'isLegacy' => $this->isLegacy,
            'isAnomaly' => $this->isAnomaly,
            'bonusAember' => $this->bonusAember,
            'bonusCapture' => $this->bonusCapture,
            'bonusDamage' => $this->bonusDamage,
            'bonusDraw' => $this->bonusDraw,
            'bonusDiscard' => $this->bonusDiscard,
            'bonusPower' => $this->bonusPower,
            'bonusHouses' => $this->bonusHouses,
        ];
    }
}
