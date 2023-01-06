<?php declare(strict_types=1);

namespace AdnanMula\Cards\Domain\Model\Keyforge\Tag;

use AdnanMula\Cards\Domain\Model\Keyforge\Tag\Base\KeyforgeTagPercentile;
use AdnanMula\Cards\Domain\Model\Shared\ValueObject\Uuid;

class KeyforgeTagPercentile99 extends KeyforgeTagPercentile
{
    public function __construct()
    {
        parent::__construct(
            Uuid::from('9819458d-a4f4-4303-957c-cea3d20b1d30'),
            'Top 99%',
        );
    }
}
