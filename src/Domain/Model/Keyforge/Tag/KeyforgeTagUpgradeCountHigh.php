<?php declare(strict_types=1);

namespace AdnanMula\Cards\Domain\Model\Keyforge\Tag;

use AdnanMula\Cards\Domain\Model\Keyforge\Tag\Base\KeyforgeTagNeutralTrait;
use AdnanMula\Cards\Domain\Model\Shared\ValueObject\Uuid;

final class KeyforgeTagUpgradeCountHigh extends KeyforgeTagNeutralTrait
{
    public function __construct()
    {
        parent::__construct(
            Uuid::from('a6ba9571-16b2-40f7-be01-9a1b31d4c7b1'),
            'Muchas mejoras',
        );
    }
}
