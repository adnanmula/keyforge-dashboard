<?php declare(strict_types=1);

namespace AdnanMula\Cards\Domain\Model\Shared\ValueObject;

enum CompetitionFixtureType: string
{
    case BEST_OF_1 = 'BEST_OF_1';
    case BEST_OF_3 = 'BEST_OF_3';
    case BEST_OF_5 = 'BEST_OF_5';
    case GAMES_2 = '2_GAMES';
    case GAMES_3 = '3_GAMES';
    case GAMES_5 = '5_GAMES';

    public static function allowedValues(): array
    {
        return [
            self::BEST_OF_1->name,
            self::BEST_OF_3->name,
            self::BEST_OF_5->name,
            self::GAMES_2->name,
            self::GAMES_3->name,
            self::GAMES_5->name,
        ];
    }
}
