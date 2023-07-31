<?php declare(strict_types=1);

namespace AdnanMula\Cards\Domain\Model\Shared\ValueObject;

use AdnanMula\Cards\Shared\EnumHelper;

enum TagVisibility: string
{
    use EnumHelper;

    case PRIVATE = 'PRIVATE';
    case PUBLIC = 'PUBLIC';
}
