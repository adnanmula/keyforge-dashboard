<?php declare(strict_types=1);

namespace AdnanMula\Cards\Domain\Model\Keyforge\Tag;

use AdnanMula\Cards\Domain\Model\Keyforge\KeyforgeTag;
use AdnanMula\Cards\Domain\Model\Shared\ValueObject\TagStyle;
use AdnanMula\Cards\Domain\Model\Shared\ValueObject\TagVisibility;
use AdnanMula\Cards\Domain\Model\Shared\ValueObject\Uuid;

final class KeyforgeTagHasRats extends KeyforgeTag
{
    public function __construct()
    {
        parent::__construct(
            Uuid::from('44beae3c-223d-4158-bbda-f5e67ad2d940'),
            'Tiene ratas',
            TagVisibility::PUBLIC,
            TagStyle::from([
                TagStyle::COLOR_BG => '#ffffff',
                TagStyle::COLOR_TEXT => '#000000',
                TagStyle::COLOR_OUTLINE => '#000000',
            ]),
        );
    }
}
