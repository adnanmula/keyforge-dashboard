<?php declare(strict_types=1);

namespace AdnanMula\Cards\Domain\Model\Shared\ValueObject;

enum CompetitionFixtureType: string
{
    case BEST_OF_1 = 'BEST_OF_1';
    case BEST_OF_3 = 'BEST_OF_3';
    case BEST_OF_5 = 'BEST_OF_5';
}
