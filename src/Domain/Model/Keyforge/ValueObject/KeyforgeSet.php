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
    case AS = 'AS';
    case U22 = 'U22';
    case M24 = 'M24';
    case VM23 = 'VM23';
    case VM24 = 'VM24';

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
            self::AS => 'Aember skies',
            self::U22 => 'Unchained',
            self::VM23 => 'Vault Masters 2023',
            self::VM24 => 'Vault Masters 2024',
            self::M24 => 'Menagerie',
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

        if ($set === 'AEMBER_SKIES') {
            return self::AS;
        }

        if ($set === 'UNCHAINED_2022') {
            return self::U22;
        }

        if ($set === 'VAULT_MASTERS_2023') {
            return self::VM23;
        }

        if ($set === 'VAULT_MASTERS_2024') {
            return self::VM24;
        }

        if ($set === 'MENAGERIE_2024') {
            return self::M24;
        }

        throw new \InvalidArgumentException($set);
    }

    public function isMain(): bool
    {
        return match ($this) {
            self::U22,self::VM23,self::VM24,self::M24 => false,
            default => true,
        };
    }
}
