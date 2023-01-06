<?php declare(strict_types=1);

namespace AdnanMula\Cards\Domain\Model\Keyforge\Tag\Base;

use AdnanMula\Cards\Domain\Model\Keyforge\KeyforgeTag;
use AdnanMula\Cards\Domain\Model\Shared\ValueObject\TagStyle;
use AdnanMula\Cards\Domain\Model\Shared\ValueObject\TagVisibility;

abstract class KeyforgeTagOwner extends KeyforgeTag
{
    public function __construct($id, $name)
    {
        parent::__construct(
            $id,
            $name,
            TagVisibility::PUBLIC,
            TagStyle::from([
                TagStyle::COLOR_BG => '#540d4e',
                TagStyle::COLOR_TEXT => '#ffffff',
                TagStyle::COLOR_OUTLINE => '#540d4e',
            ]),
        );
    }
}
