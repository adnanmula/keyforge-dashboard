<?php declare(strict_types=1);

namespace AdnanMula\Cards\Domain\Model\Keyforge\Tag;

use AdnanMula\Cards\Domain\Model\Keyforge\Tag\Base\KeyforgeTagHasCard;
use AdnanMula\Cards\Domain\Model\Shared\ValueObject\Uuid;

final class KeyforgeTagHasLegacy extends KeyforgeTagHasCard
{
    public function __construct()
    {
        parent::__construct(
            Uuid::from('0990f594-136e-4452-9b10-afacc382875b'),
            'Tiene legado',
        );
    }
}
