<?php declare(strict_types=1);

namespace AdnanMula\Cards\Domain\Model\Keyforge\Tag;

use AdnanMula\Cards\Domain\Model\Keyforge\Tag\Base\KeyforgeTagOwner;
use AdnanMula\Cards\Domain\Model\Shared\ValueObject\Uuid;

final class KeyforgeTagOwnerIsmalelo extends KeyforgeTagOwner
{
    public function __construct()
    {
        parent::__construct(
            Uuid::from('f5fd3312-e300-4775-a46a-b3ea3d2d62c8'),
            'De Ismalelo',
        );
    }
}
