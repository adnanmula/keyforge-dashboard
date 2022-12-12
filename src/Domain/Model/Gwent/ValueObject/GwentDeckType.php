<?php declare(strict_types=1);

namespace AdnanMula\Cards\Domain\Model\Gwent\ValueObject;

enum GwentDeckType: string implements \JsonSerializable
{
    case POINTSLAM = 'pointslam';
    case MIDRANGE = 'midrange';
    case MEME = 'meme';
    case CONTROL = 'control';
    case UNCATEGORIZABLE = 'uncategorizable';

    public function jsonSerialize(): mixed
    {
        return $this->name;
    }
}
