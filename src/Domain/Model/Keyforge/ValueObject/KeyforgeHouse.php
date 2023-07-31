<?php declare(strict_types=1);

namespace AdnanMula\Cards\Domain\Model\Keyforge\ValueObject;

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

    public static function fromDokName(string $house): self
    {
        $houses = \array_map(static fn (KeyforgeHouse $case) => $case->name, self::cases());

        if (\in_array(\strtoupper($house), $houses, true)) {
            return self::from(\strtoupper($house));
        }

        if ($house === 'StarAlliance') {
            return self::STAR_ALLIANCE;
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
            self::EKWIDON => 'The Merchant Compacts of Ekwidon',
        };
    }
}
