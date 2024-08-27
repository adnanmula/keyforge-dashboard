<?php declare(strict_types=1);

namespace AdnanMula\Cards\Domain\Model\Keyforge\Deck\ValueObject;

use AdnanMula\Cards\Shared\EnumHelper;

enum KeyforgeDeckType: string
{
    use EnumHelper;

    case STANDARD = 'STANDARD';
    case ALLIANCE = 'ALLIANCE';
    case THEORETICAL = 'THEORETICAL';
}
