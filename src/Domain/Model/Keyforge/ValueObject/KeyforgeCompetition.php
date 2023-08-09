<?php declare(strict_types=1);

namespace AdnanMula\Cards\Domain\Model\Keyforge\ValueObject;

use AdnanMula\Cards\Shared\EnumHelper;

enum KeyforgeCompetition: string
{
    use EnumHelper;

    case FRIENDS = 'With friends';
    case TCO_CASUAL = 'TCO Casual';
    case TCO_COMPETITIVE = 'TCO Competitive';
    case NKFL_LEAGUE_SEASON_19 = 'NKFL League Season 19';
    case NKFL_LEAGUE_SEASON_20 = 'NKFL League Season 20';
    case NKFL_LEAGUE_SEASON_21 = 'NKFL League Season 21';
    case NKFL_LEAGUE_CUP_SEASON_19 = 'NKFL League Cups Season 19';
    case NKFL_LEAGUE_CUP_SEASON_21 = 'NKFL League Cups Season 21';
    case NKFL_ARCHON_TOURNAMENT = 'NKFL Archon Tournament';
    case LOCAL_LEAGUE = 'Local League';
    case LOCAL_CUP = 'Local Cup';

    public static function fromName(string $name): self
    {
        return match ($name) {
            self::FRIENDS->name => self::FRIENDS,
            self::TCO_CASUAL->name => self::TCO_CASUAL,
            self::TCO_COMPETITIVE->name => self::TCO_COMPETITIVE,
            self::NKFL_LEAGUE_SEASON_19->name => self::NKFL_LEAGUE_SEASON_19,
            self::NKFL_LEAGUE_SEASON_20->name => self::NKFL_LEAGUE_SEASON_20,
            self::NKFL_LEAGUE_SEASON_21->name => self::NKFL_LEAGUE_SEASON_21,
            self::NKFL_LEAGUE_CUP_SEASON_19->name => self::NKFL_LEAGUE_CUP_SEASON_19,
            self::NKFL_LEAGUE_CUP_SEASON_21->name => self::NKFL_LEAGUE_CUP_SEASON_21,
            self::NKFL_ARCHON_TOURNAMENT->name => self::NKFL_ARCHON_TOURNAMENT,
            self::LOCAL_LEAGUE->name => self::LOCAL_LEAGUE,
            self::LOCAL_CUP->name => self::LOCAL_CUP,
            default => self::FRIENDS,
        };
    }
}
