<?php declare(strict_types=1);

namespace AdnanMula\Cards\Domain\Model\Shared\ValueObject;

use AdnanMula\Cards\Shared\EnumHelper;

enum TagType: string
{
    use EnumHelper;

    case OWNER = 'OWNER';
    case CUSTOM = 'CUSTOM';
    case TRAIT_NEUTRAL = 'TRAIT_NEUTRAL';
    case TRAIT_POSITIVE = 'TRAIT_POSITIVE';
    case TRAIT_NEGATIVE = 'TRAIT_NEGATIVE';
    case OTHER = 'OTHER';
}
