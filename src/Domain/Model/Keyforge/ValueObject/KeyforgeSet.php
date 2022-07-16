<?php declare(strict_types=1);

namespace AdnanMula\Cards\Domain\Model\Keyforge\ValueObject;

enum KeyforgeSet: string
{
    case CotA = 'CotA';
    case AoA = 'AoA';
    case WC = 'WC';
    case MM = 'MM';
    case DT = 'DT';

    public function fullName(): string
    {
        return match ($this) {
            self::CotA => 'Call of the Archons',
            self::AoA => 'Age of Ascension',
            self::WC => 'Worlds Collide',
            self::MM => 'Mass Mutation',
            self::DT => 'Dark Tidings',
        };
    }

    public static function fromDokName(string $set): self
    {
        if ($set === 'CALL_OF_THE_ARCHONS') {
            return KeyforgeSet::CotA;
        }

        if ($set === 'AGE_OF_ASCENSION') {
            return KeyforgeSet::AoA;
        }

        if ($set === 'WORLDS_COLLIDE') {
            return KeyforgeSet::WC;
        }

        if ($set === 'MASS_MUTATION') {
            return KeyforgeSet::MM;
        }

        if ($set === 'DARK_TIDINGS') {
            return KeyforgeSet::DT;
        }

        throw new \InvalidArgumentException($set);
    }
}
