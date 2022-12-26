<?php declare(strict_types=1);

namespace AdnanMula\Cards\Domain\Model\Keyforge;

use AdnanMula\Cards\Domain\Model\Shared\ValueObject\TagStyle;
use AdnanMula\Cards\Domain\Model\Shared\ValueObject\TagVisibility;
use AdnanMula\Cards\Domain\Model\Shared\ValueObject\Uuid;

class KeyforgeTagPercentile99 extends KeyforgeTag
{
    public function __construct()
    {
        parent::__construct(
            Uuid::from('9819458d-a4f4-4303-957c-cea3d20b1d30'),
            'Top 99%',
            TagVisibility::PUBLIC,
            TagStyle::from([
                TagStyle::COLOR_BG => '#000000',
                TagStyle::COLOR_TEXT => '#ffffff',
                TagStyle::COLOR_OUTLINE => '#000000',
            ]),
        );
    }
}
