<?php declare(strict_types=1);

namespace AdnanMula\Cards\Domain\Model\Keyforge\Deck\Tag;

use AdnanMula\Cards\Domain\Model\Keyforge\Deck\Tag\Base\KeyforgeTagNeutralTrait;
use AdnanMula\Cards\Domain\Model\Shared\ValueObject\Locale;
use AdnanMula\Cards\Domain\Model\Shared\ValueObject\Uuid;
use AdnanMula\Cards\Shared\LocalizedString;

final class KeyforgeTagUpgradeCountHigh extends KeyforgeTagNeutralTrait
{
    public function __construct()
    {
        parent::__construct(
            Uuid::from('a6ba9571-16b2-40f7-be01-9a1b31d4c7b1'),
            LocalizedString::fromArray(
                [
                    Locale::es_ES->value => 'Muchas mejoras',
                    Locale::en_GB->value => 'High upgrade count',
                ],
            ),
        );
    }
}
