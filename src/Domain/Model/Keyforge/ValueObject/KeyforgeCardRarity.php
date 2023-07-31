<?php declare(strict_types=1);

namespace AdnanMula\Cards\Domain\Model\Keyforge\ValueObject;

use AdnanMula\Cards\Shared\EnumHelper;

enum KeyforgeCardRarity: string implements \JsonSerializable
{
    use EnumHelper;

    case RARE = 'RARE';
    case COMMON = 'COMMON';
    case UNCOMMON = 'UNCOMMON';
    case FIXED = 'FIXED';
    case SPECIAL = 'SPECIAL';
    case VARIANT = 'VARIANT';
}
