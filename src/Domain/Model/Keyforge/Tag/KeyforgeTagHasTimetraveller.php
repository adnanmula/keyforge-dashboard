<?php declare(strict_types=1);

namespace AdnanMula\Cards\Domain\Model\Keyforge\Tag;

use AdnanMula\Cards\Domain\Model\Keyforge\Tag\Base\KeyforgeTagHasCard;
use AdnanMula\Cards\Domain\Model\Shared\ValueObject\Uuid;

final class KeyforgeTagHasTimetraveller extends KeyforgeTagHasCard
{
    public function __construct()
    {
        parent::__construct(
            Uuid::from('68a20151-7d7c-47c1-9802-0640d2e61f8e'),
            'Tiene viajero del tiempo',
        );
    }
}
