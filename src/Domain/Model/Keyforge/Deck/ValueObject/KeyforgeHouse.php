<?php declare(strict_types=1);

namespace AdnanMula\Cards\Domain\Model\Keyforge\Deck\ValueObject;

use AdnanMula\Cards\Shared\EnumHelper;

enum KeyforgeHouse: string implements \JsonSerializable
{
    use EnumHelper;

    case BROBNAR = 'BROBNAR';
    case DIS = 'DIS';
    case MARS = 'MARS';
    case SHADOWS = 'SHADOWS';
    case UNTAMED = 'UNTAMED';
    case SANCTUM = 'SANCTUM';
    case LOGOS = 'LOGOS';
    case SAURIAN = 'SAURIAN';
    case STAR_ALLIANCE = 'STAR_ALLIANCE';
    case UNFATHOMABLE = 'UNFATHOMABLE';
    case EKWIDON = 'EKWIDON';
    case GEISTOID = 'GEISTOID';
    case SKYBORN = 'SKYBORN';
    case REDEMPTION = 'REDEMPTION';
    case KEYRAKEN = 'KEYRAKEN';
    case IRONIX_REBELS = 'IRONIX_REBELS';
    case ELDERS = 'ELDERS';

    public static function fromDokName(string $house): self
    {
        if (\in_array(\strtoupper($house), self::values(), true)) {
            return self::from(\strtoupper($house));
        }

        if ($house === 'StarAlliance') {
            return self::STAR_ALLIANCE;
        }

        if ($house === 'IronyxRebels') {
            return self::IRONIX_REBELS;
        }

        throw new \InvalidArgumentException($house);
    }

    public function fullName(): string
    {
        return match ($this) {
            self::BROBNAR => 'Brobnar',
            self::DIS => 'Dis',
            self::MARS => 'Mars',
            self::SHADOWS => 'Shadows',
            self::UNTAMED => 'Untamed',
            self::SANCTUM => 'Sanctum',
            self::LOGOS => 'Logos',
            self::SAURIAN => 'Saurian',
            self::STAR_ALLIANCE => 'Star Alliance',
            self::UNFATHOMABLE => 'Unfathomable',
            self::EKWIDON => 'Ekwidon',
            self::GEISTOID => 'Geistoid',
            self::SKYBORN => 'Skyborn',
            self::REDEMPTION => 'Redemption',
            self::KEYRAKEN => 'Keyraken',
            self::IRONIX_REBELS => 'Ironix Rebels',
            self::ELDERS => 'Elders',
        };
    }

    public function dokName(): string
    {
        return match ($this) {
            self::BROBNAR => 'Brobnar',
            self::DIS => 'Dis',
            self::MARS => 'Mars',
            self::SHADOWS => 'Shadows',
            self::UNTAMED => 'Untamed',
            self::SANCTUM => 'Sanctum',
            self::LOGOS => 'Logos',
            self::SAURIAN => 'Saurian',
            self::STAR_ALLIANCE => 'StarAlliance',
            self::UNFATHOMABLE => 'Unfathomable',
            self::EKWIDON => 'Ekwidon',
            self::GEISTOID => 'Geistoid',
            self::SKYBORN => 'Skyborn',
            self::REDEMPTION => 'Redemption',
            self::KEYRAKEN => 'Keyraken',
            self::IRONIX_REBELS => 'IronixRebels',
            self::ELDERS => 'Elders',
        };
    }

    public function isEnabled(): bool
    {
        return match ($this) {
            self::KEYRAKEN, self::IRONIX_REBELS, self::ELDERS => false,
            default => true,
        };
    }
}
