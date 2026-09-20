<?php declare(strict_types=1);

namespace AdnanMula\Cards\Domain\Model\Keyforge\Deck\ValueObject;

use AdnanMula\Cards\Shared\EnumHelper;

enum KeyforgeCardRarity: string implements \JsonSerializable
{
    use EnumHelper;

    case RARE = 'RARE';
    case COMMON = 'COMMON';
    case UNCOMMON = 'UNCOMMON';
    case FIXED = 'FIXED';
    case SPECIAL = 'SPECIAL';
    case VARIANT = 'VARIANT';
    case EVILTWIN = 'EVILTWIN';

    public static function fromAbbreviation(string $abbreviation): self
    {
        return match ($abbreviation) {
            'R' => self::RARE,
            'C' => self::COMMON,
            'U' => self::UNCOMMON,
            'F' => self::FIXED,
            'S' => self::SPECIAL,
            'V' => self::VARIANT,
            'ET' => self::EVILTWIN,
            default => throw new \InvalidArgumentException($abbreviation),
        };
    }

    public function abbreviated(): string
    {
        return match ($this) {
            self::RARE => 'R',
            self::COMMON => 'C',
            self::UNCOMMON => 'U',
            self::FIXED => 'F',
            self::SPECIAL => 'S',
            self::VARIANT => 'V',
            self::EVILTWIN => 'ET',
        };
    }
}
