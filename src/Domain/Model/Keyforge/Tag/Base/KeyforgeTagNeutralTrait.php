<?php declare(strict_types=1);

namespace AdnanMula\Cards\Domain\Model\Keyforge\Tag\Base;

use AdnanMula\Cards\Domain\Model\Keyforge\KeyforgeTag;
use AdnanMula\Cards\Domain\Model\Shared\ValueObject\TagStyle;
use AdnanMula\Cards\Domain\Model\Shared\ValueObject\TagVisibility;

abstract class KeyforgeTagNeutralTrait extends KeyforgeTag
{
    public function __construct($id, $name)
    {
        parent::__construct(
            $id,
            $name,
            TagVisibility::PUBLIC,
            TagStyle::from([
                TagStyle::COLOR_BG => '#ffffff',
                TagStyle::COLOR_TEXT => '#000000',
                TagStyle::COLOR_OUTLINE => '#000000',
            ]),
        );
    }
}