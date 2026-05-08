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
        public bool $bonusBrobnar,
        public bool $bonusDis,
        public bool $bonusEkwidon,
        public bool $bonusGeistoid,
        public bool $bonusLogos,
        public bool $bonusMars,
        public bool $bonusSkyborn,
        public bool $bonusOuboros,
        public bool $bonusUntamed,
        public bool $bonusRedemption,
        public bool $bonusSanctum,
        public bool $bonusShadows,
        public bool $bonusSaurian,
        public bool $bonusStarAlliance,
        public bool $bonusUnfathomable,
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
            $data['bonusBrobnar'] ?? false,
            $data['bonusDis'] ?? false,
            $data['bonusEkwidon'] ?? false,
            $data['bonusGeistoid'] ?? false,
            $data['bonusLogos'] ?? false,
            $data['bonusMars'] ?? false,
            $data['bonusSkyborn'] ?? false,
            $data['bonusOuboros'] ?? false,
            $data['bonusUntamed'] ?? false,
            $data['bonusRedemption'] ?? false,
            $data['bonusSanctum'] ?? false,
            $data['bonusShadows'] ?? false,
            $data['bonusSaurian'] ?? false,
            $data['bonusStarAlliance'] ?? false,
            $data['bonusUnfathomable'] ?? false,
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
            in_array(KeyforgeHouse::BROBNAR->dokName(), $data['bonusHouses'] ?? [], true),
            in_array(KeyforgeHouse::DIS->dokName(), $data['bonusHouses'] ?? [], true),
            in_array(KeyforgeHouse::EKWIDON->dokName(), $data['bonusHouses'] ?? [], true),
            in_array(KeyforgeHouse::GEISTOID->dokName(), $data['bonusHouses'] ?? [], true),
            in_array(KeyforgeHouse::LOGOS->dokName(), $data['bonusHouses'] ?? [], true),
            in_array(KeyforgeHouse::MARS->dokName(), $data['bonusHouses'] ?? [], true),
            in_array(KeyforgeHouse::SKYBORN->dokName(), $data['bonusHouses'] ?? [], true),
            in_array(KeyforgeHouse::OUBOROS->dokName(), $data['bonusHouses'] ?? [], true),
            in_array(KeyforgeHouse::UNTAMED->dokName(), $data['bonusHouses'] ?? [], true),
            in_array(KeyforgeHouse::REDEMPTION->dokName(), $data['bonusHouses'] ?? [], true),
            in_array(KeyforgeHouse::SANCTUM->dokName(), $data['bonusHouses'] ?? [], true),
            in_array(KeyforgeHouse::SHADOWS->dokName(), $data['bonusHouses'] ?? [], true),
            in_array(KeyforgeHouse::SAURIAN->dokName(), $data['bonusHouses'] ?? [], true),
            in_array(KeyforgeHouse::STAR_ALLIANCE->dokName(), $data['bonusHouses'] ?? [], true),
            in_array(KeyforgeHouse::UNFATHOMABLE->dokName(), $data['bonusHouses'] ?? [], true),
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
            'bonusBrobnar' => $this->bonusBrobnar,
            'bonusDis' => $this->bonusDis,
            'bonusEkwidon' => $this->bonusEkwidon,
            'bonusGeistoid' => $this->bonusGeistoid,
            'bonusLogos' => $this->bonusLogos,
            'bonusMars' => $this->bonusMars,
            'bonusSkyborn' => $this->bonusSkyborn,
            'bonusOuboros' => $this->bonusOuboros,
            'bonusUntamed' => $this->bonusUntamed,
            'bonusRedemption' => $this->bonusRedemption,
            'bonusSanctum' => $this->bonusSanctum,
            'bonusShadows' => $this->bonusShadows,
            'bonusSaurian' => $this->bonusSaurian,
            'bonusStarAlliance' => $this->bonusStarAlliance,
            'bonusUnfathomable' => $this->bonusUnfathomable,
        ];
    }
}
