<?php declare(strict_types=1);

namespace AdnanMula\Cards\Domain\Model\Keyforge\Deck\ValueObject;

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
    case ToC = 'ToC';
    case MoM = 'MoM';
    case U22 = 'U22';
    case M24 = 'M24';
    case VM23 = 'VM23';
    case VM24 = 'VM24';
    case ANOMALY_EXPANSION = 'ANOMALY_EXPANSION';
    case MARTIAN_CIVIL_WAR = 'MARTIAN_CIVIL_WAR';

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
            self::M24 => 'Menagerie',
            self::VM23 => 'Vault Masters 2023',
            self::VM24 => 'Vault Masters 2024',
            self::ANOMALY_EXPANSION => 'Anomaly',
            self::MARTIAN_CIVIL_WAR => 'Martian Civil War',
            self::ToC => 'Tokens of change',
            self::MoM => 'More Mutation',
        };
    }

    public static function fromDokName(string $set): self
    {
        return match ($set) {
            'CALL_OF_THE_ARCHONS' => self::CotA,
            'AGE_OF_ASCENSION' => self::AoA,
            'WORLDS_COLLIDE' => self::WC,
            'MASS_MUTATION' => self::MM,
            'DARK_TIDINGS' => self::DT,
            'WINDS_OF_EXCHANGE' => self::WoE,
            'GRIM_REMINDERS' => self::GR,
            'AEMBER_SKIES' => self::AS,
            'UNCHAINED_2022' => self::U22,
            'VAULT_MASTERS_2023' => self::VM23,
            'VAULT_MASTERS_2024' => self::VM24,
            'MENAGERIE_2024' => self::M24,
            'ANOMALY_EXPANSION' => self::ANOMALY_EXPANSION,
            'MARTIAN_CIVIL_WAR' => self::MARTIAN_CIVIL_WAR,
            'TOKENS_OF_CHANGE' => self::ToC,
            'MORE_MUTATION' => self::MoM,
            default => throw new \InvalidArgumentException($set),
        };
    }

    public function isMain(): bool
    {
        return match ($this) {
            self::U22, self::VM23, self::VM24, self::M24, self::MARTIAN_CIVIL_WAR, self::ToC, self::MoM => false,
            default => true,
        };
    }

    public function isEnabled(): bool
    {
        return match ($this) {
            self::ANOMALY_EXPANSION, self::MARTIAN_CIVIL_WAR => false,
            default => true,
        };
    }
}
