<?php declare(strict_types=1);

namespace AdnanMula\Cards\Domain\Model\Keyforge\Deck\ValueObject;

final readonly class KeyforgeCard implements \JsonSerializable
{
    private function __construct(
        public string $serializedName,
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
            $data['sN'],
            KeyforgeCardRarity::fromAbbreviation(\strtoupper($data['r'])),
            $data['iE'] ?? false,
            $data['iM'] ?? false,
            $data['iL'] ?? false,
            $data['iA'] ?? false,
            $data['bA'] ?? 0,
            $data['bC'] ?? 0,
            $data['bDa'] ?? 0,
            $data['bDr'] ?? 0,
            $data['bDi'] ?? 0,
            $data['bP'] ?? 0,
            $data['bH'] ?? [],
        );
    }

    public static function fromDokData(array $data): self
    {
        $urlPieces = explode('/', $data['cardTitleUrl']);
        $serializedName = explode('.', end($urlPieces))[0];

        return new self(
            $serializedName,
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
        return array_filter([
            'sN' => $this->serializedName,
            'r' => $this->rarity->abbreviated(),
            'iE' => $this->isEnhanced,
            'iM' => $this->isMaverick,
            'iL' => $this->isLegacy,
            'iA' => $this->isAnomaly,
            'bA' => $this->bonusAember,
            'bC' => $this->bonusCapture,
            'bDa' => $this->bonusDamage,
            'bDr' => $this->bonusDraw,
            'bDi' => $this->bonusDiscard,
            'bP' => $this->bonusPower,
            'bH' => $this->bonusHouses,
        ]);
    }
}
