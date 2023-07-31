<?php declare(strict_types=1);

namespace AdnanMula\Cards\Domain\Model\Keyforge\ValueObject;

use AdnanMula\Cards\Shared\EnumHelper;

enum KeyforgeSet: string
{
    use EnumHelper;

    case CotA = 'CotA';
    case AoA = 'AoA';
    case WC = 'WC';
    case MM = 'MM';
    case DT = 'DT';
    case WoE = 'WoE';

    public function fullName(): string
    {
        return match ($this) {
            self::CotA => 'Call of the Archons',
            self::AoA => 'Age of Ascension',
            self::WC => 'Worlds Collide',
            self::MM => 'Mass Mutation',
            self::DT => 'Dark Tidings',
            self::WoE => 'Winds of Exchange',
        };
    }

    public static function fromDokName(string $set): self
    {
        if ($set === 'CALL_OF_THE_ARCHONS') {
            return self::CotA;
        }

        if ($set === 'AGE_OF_ASCENSION') {
            return self::AoA;
        }

        if ($set === 'WORLDS_COLLIDE') {
            return self::WC;
        }

        if ($set === 'MASS_MUTATION') {
            return self::MM;
        }

        if ($set === 'DARK_TIDINGS') {
            return self::DT;
        }

        if ($set === 'WINDS_OF_EXCHANGE') {
            return self::WoE;
        }

        throw new \InvalidArgumentException($set);
    }
}
