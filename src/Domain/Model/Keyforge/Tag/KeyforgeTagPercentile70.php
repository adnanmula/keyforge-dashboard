<?php declare(strict_types=1);

namespace AdnanMula\Cards\Domain\Model\Keyforge\Tag;

use AdnanMula\Cards\Domain\Model\Keyforge\Tag\Base\KeyforgeTagPercentile;
use AdnanMula\Cards\Domain\Model\Shared\ValueObject\Uuid;

class KeyforgeTagPercentile70 extends KeyforgeTagPercentile
{
    public function __construct()
    {
        parent::__construct(
            Uuid::from('9ec95522-eb01-4ee3-a360-a5da496509d7'),
            'Top 70%',
        );
    }
}
