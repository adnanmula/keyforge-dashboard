<?php declare(strict_types=1);

namespace AdnanMula\Cards\Domain\Model\Keyforge\Tag;

use AdnanMula\Cards\Domain\Model\Keyforge\Tag\Base\KeyforgeTagPositiveTrait;
use AdnanMula\Cards\Domain\Model\Shared\ValueObject\Uuid;

final class KeyforgeTagCreatureProtectionHigh extends KeyforgeTagPositiveTrait
{
    public function __construct()
    {
        parent::__construct(
            Uuid::from('7c169d7d-0070-4e5b-a1fe-c204140548ef'),
            'Proteccion de criaturas',
        );
    }
}
