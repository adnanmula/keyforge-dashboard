<?php declare(strict_types=1);

namespace AdnanMula\Cards\Domain\Model\Keyforge\Tag;

use AdnanMula\Cards\Domain\Model\Keyforge\Tag\Base\KeyforgeTagPercentile;
use AdnanMula\Cards\Domain\Model\Shared\ValueObject\Uuid;

class KeyforgeTagPercentile90 extends KeyforgeTagPercentile
{
    public function __construct()
    {
        parent::__construct(
            Uuid::from('395b9fed-d972-4d0c-b19e-6a6266973650'),
            'Top 90%',
        );
    }
}
