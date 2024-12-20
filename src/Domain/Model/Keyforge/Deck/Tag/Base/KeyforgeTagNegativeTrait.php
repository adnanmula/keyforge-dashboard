<?php declare(strict_types=1);

namespace AdnanMula\Cards\Domain\Model\Keyforge\Deck\Tag\Base;

use AdnanMula\Cards\Domain\Model\Keyforge\Deck\KeyforgeDeckTag;
use AdnanMula\Cards\Domain\Model\Shared\ValueObject\TagStyle;
use AdnanMula\Cards\Domain\Model\Shared\ValueObject\TagType;
use AdnanMula\Cards\Domain\Model\Shared\ValueObject\TagVisibility;
use AdnanMula\Cards\Domain\Model\Shared\ValueObject\Uuid;
use AdnanMula\Cards\Shared\LocalizedString;

abstract class KeyforgeTagNegativeTrait extends KeyforgeDeckTag
{
    public function __construct(Uuid $id, LocalizedString $name)
    {
        parent::__construct(
            $id,
            $name,
            TagVisibility::PUBLIC,
            TagStyle::from([
                TagStyle::COLOR_BG => '#f50529',
                TagStyle::COLOR_TEXT => '#ffffff',
                TagStyle::COLOR_OUTLINE => '#f50529',
            ]),
            TagType::TRAIT_NEGATIVE,
            false,
        );
    }
}
