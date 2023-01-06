<?php declare(strict_types=1);

namespace AdnanMula\Cards\Domain\Model\Keyforge\Tag;

use AdnanMula\Cards\Domain\Model\Keyforge\Tag\Base\KeyforgeTagNeutralTrait;
use AdnanMula\Cards\Domain\Model\Shared\ValueObject\Uuid;

final class KeyforgeTagActionCountHigh extends KeyforgeTagNeutralTrait
{
    public function __construct()
    {
        parent::__construct(
            Uuid::from('f428f793-118c-499f-af21-2fe2805ef8a2'),
            'Muchas acciones',
        );
    }
}
