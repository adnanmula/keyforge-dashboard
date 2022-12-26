<?php declare(strict_types=1);

namespace AdnanMula\Cards\Domain\Model\Keyforge;

use AdnanMula\Cards\Domain\Model\Shared\ValueObject\TagStyle;
use AdnanMula\Cards\Domain\Model\Shared\ValueObject\TagVisibility;
use AdnanMula\Cards\Domain\Model\Shared\ValueObject\Uuid;

class KeyforgeTagPercentile80 extends KeyforgeTag
{
    public function __construct()
    {
        parent::__construct(
            Uuid::from('b02423cd-3cc9-42f9-a3ff-c8eda60857ef'),
            'Top 80%',
            TagVisibility::PUBLIC,
            TagStyle::from([
                TagStyle::COLOR_BG => '#8da832',
                TagStyle::COLOR_TEXT => '#000000',
                TagStyle::COLOR_OUTLINE => '#000000',
            ]),
        );
    }
}
