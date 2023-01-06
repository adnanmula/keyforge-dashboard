<?php declare(strict_types=1);

namespace AdnanMula\Cards\Domain\Model\Keyforge\Tag;

use AdnanMula\Cards\Domain\Model\Keyforge\Tag\Base\KeyforgeTagOwner;
use AdnanMula\Cards\Domain\Model\Shared\ValueObject\Uuid;

final class KeyforgeTagOwnerNan extends KeyforgeTagOwner
{
    public function __construct()
    {
        parent::__construct(
            Uuid::from('66def513-e8bd-4ddc-8804-954600478f0d'),
            'De Nan',
        );
    }
}
