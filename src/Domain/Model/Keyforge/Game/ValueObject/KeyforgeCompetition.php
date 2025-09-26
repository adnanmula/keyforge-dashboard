<?php declare(strict_types=1);

namespace AdnanMula\Cards\Domain\Model\Keyforge\Game\ValueObject;

use AdnanMula\Cards\Shared\EnumHelper;

enum KeyforgeCompetition: string
{
    use EnumHelper;

    case SOLO = 'Solo';
    case FRIENDS = 'With friends';
    case TCO_CASUAL = 'TCO Casual';
    case TCO_COMPETITIVE = 'TCO Competitive';
    case LOCAL_LEAGUE = 'Local League';
    case FRIENDS_LEAGUE = 'League with friends';
    case VT = 'VT';
    case NATIONAL = 'NATIONAL';
    case LGS = 'LGS';
    case NKFL = 'NKFL';

    public static function fromName(string $name): self
    {
        return match ($name) {
            self::SOLO->name => self::SOLO,
            self::FRIENDS->name => self::FRIENDS,
            self::TCO_CASUAL->name => self::TCO_CASUAL,
            self::TCO_COMPETITIVE->name => self::TCO_COMPETITIVE,
            self::LOCAL_LEAGUE->name => self::LOCAL_LEAGUE,
            self::FRIENDS_LEAGUE->name => self::FRIENDS_LEAGUE,
            self::VT->name => self::VT,
            self::NATIONAL->name => self::NATIONAL,
            self::LGS->name => self::LGS,
            self::NKFL->name => self::NKFL,
            default => self::FRIENDS,
        };
    }
}
