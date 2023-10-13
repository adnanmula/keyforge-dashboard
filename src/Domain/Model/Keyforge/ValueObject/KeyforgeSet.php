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
    case GR = 'GR';
    case U22 = 'U22';
    case VM23 = 'VM23';

    public function fullName(): string
    {
        return match ($this) {
            self::CotA => 'Call of the Archons',
            self::AoA => 'Age of Ascension',
            self::WC => 'Worlds Collide',
            self::MM => 'Mass Mutation',
            self::DT => 'Dark Tidings',
            self::WoE => 'Winds of Exchange',
            self::GR => 'Grim Remainders',
            self::U22 => 'Unchained',
            self::VM23 => 'Vault Masters 2023',
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

        if ($set === 'GRIM_REMINDERS') {
            return self::GR;
        }

        if ($set === 'UNCHAINED_2022') {
            return self::U22;
        }

        if ($set === 'VAULT_MASTERS_2023') {
            return self::VM23;
        }

        throw new \InvalidArgumentException($set);
    }
}
