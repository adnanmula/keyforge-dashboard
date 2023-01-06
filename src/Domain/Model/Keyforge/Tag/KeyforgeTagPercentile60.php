<?php declare(strict_types=1);

namespace AdnanMula\Cards\Domain\Model\Keyforge\Tag;

use AdnanMula\Cards\Domain\Model\Keyforge\Tag\Base\KeyforgeTagPercentile;
use AdnanMula\Cards\Domain\Model\Shared\ValueObject\Uuid;

class KeyforgeTagPercentile60 extends KeyforgeTagPercentile
{
    public function __construct()
    {
        parent::__construct(
            Uuid::from('79992aa7-76c4-4d23-aef9-6851d5846dbb'),
            'Top 60%',
        );
    }
}
