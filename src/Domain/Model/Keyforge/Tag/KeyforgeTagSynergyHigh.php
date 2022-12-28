<?php declare(strict_types=1);

namespace AdnanMula\Cards\Domain\Model\Keyforge\Tag;

use AdnanMula\Cards\Domain\Model\Keyforge\KeyforgeTag;
use AdnanMula\Cards\Domain\Model\Shared\ValueObject\TagStyle;
use AdnanMula\Cards\Domain\Model\Shared\ValueObject\TagVisibility;
use AdnanMula\Cards\Domain\Model\Shared\ValueObject\Uuid;

final class KeyforgeTagSynergyHigh extends KeyforgeTag
{
    public function __construct()
    {
        parent::__construct(
            Uuid::from('a73fd6c3-53f8-41bc-b1b0-b8fbe63e676d'),
            'Sinérgico',
            TagVisibility::PUBLIC,
            TagStyle::from([
                TagStyle::COLOR_BG => '#1e75e6',
                TagStyle::COLOR_TEXT => '#ffffff',
                TagStyle::COLOR_OUTLINE => '#1e75e6',
            ]),
        );
    }
}
