<?php declare(strict_types=1);

namespace AdnanMula\Cards\Domain\Model\Keyforge\Tag;

use AdnanMula\Cards\Domain\Model\Keyforge\KeyforgeTag;
use AdnanMula\Cards\Domain\Model\Shared\ValueObject\TagStyle;
use AdnanMula\Cards\Domain\Model\Shared\ValueObject\TagVisibility;
use AdnanMula\Cards\Domain\Model\Shared\ValueObject\Uuid;

final class KeyforgeTagHasScalingAmberControl extends KeyforgeTag
{
    public function __construct()
    {
        parent::__construct(
            Uuid::from('fa921c9c-1d0d-482a-9462-6767b2c0a080'),
            'Tiene control de ambar escalado',
            TagVisibility::PUBLIC,
            TagStyle::from([
                TagStyle::COLOR_BG => '#1e75e6',
                TagStyle::COLOR_TEXT => '#ffffff',
                TagStyle::COLOR_OUTLINE => '#1e75e6',
            ]),
        );
    }
}
