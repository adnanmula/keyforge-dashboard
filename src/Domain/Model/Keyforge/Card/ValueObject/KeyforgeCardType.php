<?php declare(strict_types=1);

namespace AdnanMula\Cards\Domain\Model\Keyforge\Card\ValueObject;

use AdnanMula\Cards\Shared\EnumHelper;

enum KeyforgeCardType: string implements \JsonSerializable
{
    use EnumHelper;

    case CREATURE = 'CREATURE';
    case ACTION = 'ACTION';
    case ARTIFACT = 'ARTIFACT';
    case UPGRADE = 'UPGRADE';
    case TOKENCREATURE = 'TOKENCREATURE';
}
