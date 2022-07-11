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
        return match($this)
        {
            self::CotA => 'Call of the Archons',
            self::AoA => 'Age of Ascension',
            self::WC => 'Worlds Collide',
            self::MM => 'Mass Mutation',
            self::DT => 'Dark Tidings',
        };
    }
}
