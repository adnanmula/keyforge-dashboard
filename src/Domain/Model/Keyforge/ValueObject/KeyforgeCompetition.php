<?php declare(strict_types=1);

namespace AdnanMula\Cards\Domain\Model\Keyforge\ValueObject;

enum KeyforgeCompetition: string
{
    case FRIENDS = 'With friends';
    case TCO_CASUAL = 'TCO Casual';
    case TCO_COMPETITIVE = 'TCO Competitive';
    case NKFL_LEAGUE_SEASON_19 = 'NKFL League Season 19';
    case NKFL_LEAGUE_CUP_SEASON_19 = 'NKFL League Cup Season 19';
    case NKFL_ARCHON_TOURNAMENT = 'NKFL Archon Tournament';

    public static function fromName(string $name): static
    {
        switch ($name) {
            case self::FRIENDS->name:
                return self::FRIENDS;
            case self::TCO_CASUAL->name:
                return self::TCO_CASUAL;
            case self::TCO_COMPETITIVE->name:
                return self::TCO_COMPETITIVE;
            case self::NKFL_LEAGUE_SEASON_19->name:
                return self::NKFL_LEAGUE_SEASON_19;
            case self::NKFL_LEAGUE_CUP_SEASON_19->name:
                return self::NKFL_LEAGUE_CUP_SEASON_19;
            case self::NKFL_ARCHON_TOURNAMENT->name:
                return self::NKFL_ARCHON_TOURNAMENT;
            default:
                return self::FRIENDS;
        }
    }

    public static function allowedValues(): array
    {
        return [
            self::FRIENDS->name,
            self::TCO_CASUAL->name,
            self::TCO_COMPETITIVE->name,
            self::NKFL_LEAGUE_SEASON_19->name,
            self::NKFL_LEAGUE_CUP_SEASON_19->name,
            self::NKFL_ARCHON_TOURNAMENT->name,
        ];
    }
}
