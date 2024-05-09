<?php declare(strict_types=1);

namespace AdnanMula\Cards\Domain\Model\Shared\ValueObject;

use AdnanMula\Cards\Shared\EnumHelper;

enum UserRole: string
{
    use EnumHelper;

    case ROLE_ADMIN = 'ROLE_ADMIN';
    case ROLE_KEYFORGE = 'ROLE_KEYFORGE';
    case ROLE_BASIC = 'ROLE_BASIC';
}
