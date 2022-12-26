<?php declare(strict_types=1);

namespace AdnanMula\Cards\Domain\Model\Keyforge;

use AdnanMula\Cards\Domain\Model\Shared\ValueObject\TagStyle;
use AdnanMula\Cards\Domain\Model\Shared\ValueObject\TagVisibility;
use AdnanMula\Cards\Domain\Model\Shared\ValueObject\Uuid;

class KeyforgeTagPercentile70 extends KeyforgeTag
{
    public function __construct()
    {
        parent::__construct(
            Uuid::from('9ec95522-eb01-4ee3-a360-a5da496509d7'),
            'Top 70%',
            TagVisibility::PUBLIC,
            TagStyle::from([
                TagStyle::COLOR_BG => '#8da832',
                TagStyle::COLOR_TEXT => '#000000',
                TagStyle::COLOR_OUTLINE => '#000000',
            ]),
        );
    }
}
