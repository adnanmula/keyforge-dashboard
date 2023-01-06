<?php declare(strict_types=1);

namespace AdnanMula\Cards\Domain\Model\Keyforge\Tag;

use AdnanMula\Cards\Domain\Model\Keyforge\Tag\Base\KeyforgeTagHasCard;
use AdnanMula\Cards\Domain\Model\Shared\ValueObject\Uuid;

final class KeyforgeTagHasKeyCheats extends KeyforgeTagHasCard
{
    public function __construct()
    {
        parent::__construct(
            Uuid::from('708e5ef2-f392-4c25-a08e-9ba1ca7e7725'),
            'Tiene key cheats',
        );
    }
}
