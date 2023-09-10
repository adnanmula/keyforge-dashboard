<?php declare(strict_types=1);

namespace AdnanMula\Cards\Domain\Model\Shared\ValueObject;

use AdnanMula\Cards\Shared\EnumHelper;

enum Locale: string
{
    use EnumHelper;

    case es_ES = 'es_ES';
    case en_GB = 'en_GB';
}
