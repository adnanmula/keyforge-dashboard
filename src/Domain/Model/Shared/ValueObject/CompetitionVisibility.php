<?php declare(strict_types=1);

namespace AdnanMula\Cards\Domain\Model\Shared\ValueObject;

use AdnanMula\Cards\Shared\EnumHelper;

enum CompetitionVisibility: string
{
    use EnumHelper;

    case PUBLIC = 'PUBLIC';
    case PRIVATE = 'PRIVATE';
    case FRIENDS = 'FRIENDS';
}
