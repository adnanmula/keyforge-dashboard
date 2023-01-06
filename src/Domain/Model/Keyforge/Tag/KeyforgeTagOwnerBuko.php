<?php declare(strict_types=1);

namespace AdnanMula\Cards\Domain\Model\Keyforge\Tag;

use AdnanMula\Cards\Domain\Model\Keyforge\Tag\Base\KeyforgeTagOwner;
use AdnanMula\Cards\Domain\Model\Shared\ValueObject\Uuid;

final class KeyforgeTagOwnerBuko extends KeyforgeTagOwner
{
    public function __construct()
    {
        parent::__construct(
            Uuid::from('a8c46a12-a768-4409-9f59-4d247b40f6ce'),
            'De Bukisito',
        );
    }
}
