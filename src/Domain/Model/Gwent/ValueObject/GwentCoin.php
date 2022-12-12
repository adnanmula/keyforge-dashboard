<?php declare(strict_types=1);

namespace AdnanMula\Cards\Domain\Model\Gwent\ValueObject;

enum GwentCoin: string implements \JsonSerializable
{
    case BLUE = 'blue';
    case RED = 'red';

    public function jsonSerialize(): mixed
    {
        return $this->name;
    }
}
