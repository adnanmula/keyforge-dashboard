<?php declare(strict_types=1);

namespace AdnanMula\Cards\Domain\Model\Keyforge\Tag;

use AdnanMula\Cards\Domain\Model\Keyforge\Tag\Base\KeyforgeTagHasCard;
use AdnanMula\Cards\Domain\Model\Shared\ValueObject\Uuid;

final class KeyforgeTagHasXenos extends KeyforgeTagHasCard
{
    public function __construct()
    {
        parent::__construct(
            Uuid::from('028b4bd4-ea57-46b1-8a41-27d5bba4a3a8'),
            'Tiene Xenos Bloodshadow',
        );
    }
}
