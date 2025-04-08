<?php declare(strict_types=1);

namespace AdnanMula\Cards\Domain\Model\Keyforge\Deck\Tag;

use AdnanMula\Cards\Domain\Model\Keyforge\Deck\Tag\Base\KeyforgeTagPositiveTrait;
use AdnanMula\Cards\Domain\Model\Shared\ValueObject\Locale;
use AdnanMula\Cards\Domain\Model\Shared\ValueObject\Uuid;
use AdnanMula\Cards\Shared\LocalizedString;

final class KeyforgeTagHasGigantic extends KeyforgeTagPositiveTrait
{
    public function __construct()
    {
        parent::__construct(
            Uuid::from('36f514d7-9c84-486a-acfe-f4616fa2774b'),
            LocalizedString::fromArray(
                [
                    Locale::es_ES->value => 'Tiene una criatura gigante',
                    Locale::en_GB->value => 'Has a gigantic creature',
                ],
            ),
        );
    }
}
