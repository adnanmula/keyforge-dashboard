<?php declare(strict_types=1);

namespace AdnanMula\Cards\Domain\Model\Keyforge\ValueObject;

use JetBrains\PhpStorm\Internal\TentativeType;

enum KeyforgeHouse: string implements \JsonSerializable
{
    case BROBNAR = 'BROBNAR';
    case DIS = 'DIS';
    case MARS = 'MARS';
    case SHADOWS = 'SHADOWS';
    case UNTAMED = 'UNTAMED';
    case SANCTUM = 'SANCTUM';
    case LOGOS = 'LOGOS';
    case SAURIAN = 'SAURIAN';
    case STAR_ALLIANCE = 'STAR_ALLIANCE';
    case UNFATHOMABLE = 'UNFATHOMABLE';

    public function jsonSerialize(): mixed
    {
        return $this->name;
    }
}
