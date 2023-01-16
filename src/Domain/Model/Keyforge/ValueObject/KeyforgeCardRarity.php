<?php declare(strict_types=1);

namespace AdnanMula\Cards\Domain\Model\Keyforge\ValueObject;

enum KeyforgeCardRarity: string implements \JsonSerializable
{
    case RARE = 'RARE';
    case COMMON = 'COMMON';
    case UNCOMMON = 'UNCOMMON';
    case FIXED = 'FIXED';
    case SPECIAL = 'SPECIAL';
    case VARIANT = 'VARIANT';

    public function jsonSerialize(): string
    {
        return $this->name;
    }
}
