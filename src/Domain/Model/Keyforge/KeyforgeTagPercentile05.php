<?php declare(strict_types=1);

namespace AdnanMula\Cards\Domain\Model\Keyforge;

use AdnanMula\Cards\Domain\Model\Shared\ValueObject\TagStyle;
use AdnanMula\Cards\Domain\Model\Shared\ValueObject\TagVisibility;
use AdnanMula\Cards\Domain\Model\Shared\ValueObject\Uuid;

class KeyforgeTagPercentile05 extends KeyforgeTag
{
    public function __construct()
    {
        parent::__construct(
            Uuid::from('31aa1561-d4a6-43c0-9597-5cd8eac0937b'),
            'Bottom 5%',
            TagVisibility::PUBLIC,
            TagStyle::from([
                TagStyle::COLOR_BG => '#000000',
                TagStyle::COLOR_TEXT => '#ffffff',
                TagStyle::COLOR_OUTLINE => '#000000',
            ]),
        );
    }
}
