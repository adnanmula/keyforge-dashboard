<?php declare(strict_types=1);

namespace AdnanMula\Cards\Domain\Model\Shared\ValueObject;

use AdnanMula\Cards\Shared\EnumHelper;

enum CompetitionFixtureType: string
{
    use EnumHelper;

    case BEST_OF_1 = 'BEST_OF_1';
    case BEST_OF_3 = 'BEST_OF_3';
    case BEST_OF_5 = 'BEST_OF_5';
    case GAMES_3 = 'GAMES_3';
    case GAMES_5 = 'GAMES_5';

    public function isBestOf(): bool
    {
        return $this === self::BEST_OF_1
            || $this === self::BEST_OF_3
            || $this === self::BEST_OF_5;
    }
}
