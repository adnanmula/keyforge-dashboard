<?php declare(strict_types=1);

namespace AdnanMula\Cards\Domain\Model\Keyforge\Tag;

use AdnanMula\Cards\Domain\Model\Keyforge\Tag\Base\KeyforgeTagNeutralTrait;
use AdnanMula\Cards\Domain\Model\Shared\ValueObject\Uuid;

final class KeyforgeTagCreatureCountHigh extends KeyforgeTagNeutralTrait
{
    public function __construct()
    {
        parent::__construct(
            Uuid::from('2b60df5d-ac78-491e-a3f7-fba2709601ee'),
            'Muchas criaturas',
        );
    }
}
