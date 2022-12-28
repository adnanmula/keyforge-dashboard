<?php declare(strict_types=1);

namespace AdnanMula\Cards\Domain\Model\Keyforge\Tag;

use AdnanMula\Cards\Domain\Model\Keyforge\KeyforgeTag;
use AdnanMula\Cards\Domain\Model\Shared\ValueObject\TagStyle;
use AdnanMula\Cards\Domain\Model\Shared\ValueObject\TagVisibility;
use AdnanMula\Cards\Domain\Model\Shared\ValueObject\Uuid;

final class KeyforgeTagAntiSynergyHigh extends KeyforgeTag
{
    public function __construct()
    {
        parent::__construct(
            Uuid::from('d82f01fd-a505-4256-86c2-8f07b64e6ecf'),
            'Antisinérgico',
            TagVisibility::PUBLIC,
            TagStyle::from([
                TagStyle::COLOR_BG => '#d42651',
                TagStyle::COLOR_TEXT => '#ffffff',
                TagStyle::COLOR_OUTLINE => '#d42651',
            ]),
        );
    }
}
