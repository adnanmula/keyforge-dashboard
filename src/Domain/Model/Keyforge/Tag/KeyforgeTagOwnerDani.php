<?php declare(strict_types=1);

namespace AdnanMula\Cards\Domain\Model\Keyforge\Tag;

use AdnanMula\Cards\Domain\Model\Keyforge\Tag\Base\KeyforgeTagOwner;
use AdnanMula\Cards\Domain\Model\Shared\ValueObject\Uuid;

final class KeyforgeTagOwnerDani extends KeyforgeTagOwner
{
    public function __construct()
    {
        parent::__construct(
            Uuid::from('06ddb14c-a882-46bc-85a9-18bdd51ee826'),
            'De Dani',
        );
    }
}
