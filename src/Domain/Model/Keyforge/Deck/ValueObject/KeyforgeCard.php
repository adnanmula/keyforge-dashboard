<?php declare(strict_types=1);

namespace AdnanMula\Cards\Domain\Model\Keyforge\Deck\ValueObject;

final readonly class KeyforgeCard implements \JsonSerializable
{
    private function __construct(
        public string $name,
        public string $serializedName,
        public string $imageUrl,
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
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            $data['name'],
            $data['serializedName'],
            $data['imageUrl'],
            KeyforgeCardRarity::from(\strtoupper($data['rarity'])),
            $data['isEnhanced'],
            $data['isMaverick'],
            $data['isLegacy'],
            $data['isAnomaly'],
            $data['bonusAember'],
            $data['bonusCapture'],
            $data['bonusDamage'],
            $data['bonusDraw'],
            $data['bonusDiscard'],
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
        ];
    }
}
