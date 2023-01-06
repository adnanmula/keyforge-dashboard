<?php declare(strict_types=1);

namespace AdnanMula\Cards\Domain\Model\Keyforge\Tag;

use AdnanMula\Cards\Domain\Model\Keyforge\Tag\Base\KeyforgeTagOwner;
use AdnanMula\Cards\Domain\Model\Shared\ValueObject\Uuid;

final class KeyforgeTagOwnerNull extends KeyforgeTagOwner
{
    public function __construct()
    {
        parent::__construct(
            Uuid::from('55e2195e-cb7e-4f15-ae44-1d1340380efa'),
            'De nadie',
        );
    }
}
