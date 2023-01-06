<?php declare(strict_types=1);

namespace AdnanMula\Cards\Domain\Model\Keyforge\Tag;

use AdnanMula\Cards\Domain\Model\Keyforge\Tag\Base\KeyforgeTagOwner;
use AdnanMula\Cards\Domain\Model\Shared\ValueObject\Uuid;

final class KeyforgeTagOwnerChopi extends KeyforgeTagOwner
{
    public function __construct()
    {
        parent::__construct(
            Uuid::from('30a50e5e-608c-4ff7-967e-5086ebd75510'),
            'De Chopi',
        );
    }
}
