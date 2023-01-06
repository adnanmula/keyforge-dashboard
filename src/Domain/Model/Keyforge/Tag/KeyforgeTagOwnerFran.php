<?php declare(strict_types=1);

namespace AdnanMula\Cards\Domain\Model\Keyforge\Tag;

use AdnanMula\Cards\Domain\Model\Keyforge\Tag\Base\KeyforgeTagOwner;
use AdnanMula\Cards\Domain\Model\Shared\ValueObject\Uuid;

final class KeyforgeTagOwnerFran extends KeyforgeTagOwner
{
    public function __construct()
    {
        parent::__construct(
            Uuid::from('2bfff298-83a8-4dbe-85ca-bdc2f65353fe'),
            'De Fran',
        );
    }
}
