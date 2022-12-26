<?php declare(strict_types=1);

namespace AdnanMula\Cards\Domain\Model\Keyforge;

use AdnanMula\Cards\Domain\Model\Shared\ValueObject\TagStyle;
use AdnanMula\Cards\Domain\Model\Shared\ValueObject\TagVisibility;
use AdnanMula\Cards\Domain\Model\Shared\ValueObject\Uuid;

class KeyforgeTagPercentile90 extends KeyforgeTag
{
    public function __construct()
    {
        parent::__construct(
            Uuid::from('395b9fed-d972-4d0c-b19e-6a6266973650'),
            'Top 90%',
            TagVisibility::PUBLIC,
            TagStyle::from([
                TagStyle::COLOR_BG => '#8da832',
                TagStyle::COLOR_TEXT => '#000000',
                TagStyle::COLOR_OUTLINE => '#000000',
            ]),
        );
    }
}
