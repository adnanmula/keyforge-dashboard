<?php declare(strict_types=1);

namespace AdnanMula\Cards\Domain\Model\Keyforge\Stat\ValueObject;

use AdnanMula\Cards\Shared\EnumHelper;

enum KeyforgeStatCategory: string
{
    use EnumHelper;

    case HOME_GENERAL_DATA = 'HOME_GENERAL_DATA';
    case USER_PROFILE = 'USER_PROFILE';
    case DECK_DETAIL = 'DECK_DETAIL';
}
