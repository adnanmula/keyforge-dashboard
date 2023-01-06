<?php declare(strict_types=1);

namespace AdnanMula\Cards\Domain\Model\Keyforge\Tag;

use AdnanMula\Cards\Domain\Model\Keyforge\Tag\Base\KeyforgeTagPercentile;
use AdnanMula\Cards\Domain\Model\Shared\ValueObject\Uuid;

class KeyforgeTagPercentile80 extends KeyforgeTagPercentile
{
    public function __construct()
    {
        parent::__construct(
            Uuid::from('b02423cd-3cc9-42f9-a3ff-c8eda60857ef'),
            'Top 80%',
        );
    }
}
