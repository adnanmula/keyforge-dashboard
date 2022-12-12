<?php declare(strict_types=1);

namespace AdnanMula\Cards\Domain\Model\Gwent\ValueObject;

enum GwentFaction: string implements \JsonSerializable
{
    case MONSTER = 'MONSTER';
    case SCOIATAEL = 'SCOIATAEL';
    case SKELLIGE = 'SKELLIGE';
    case SYNDICATE = 'SYNDICATE';
    case NORTHERN_REALMS = 'NORTHERN_REALMS';
    case NILFGARD = 'NILFGARD';

    public function jsonSerialize(): mixed
    {
        return $this->name;
    }
}
