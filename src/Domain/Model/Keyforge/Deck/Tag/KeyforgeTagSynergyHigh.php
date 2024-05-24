<?php declare(strict_types=1);

namespace AdnanMula\Cards\Domain\Model\Keyforge\Deck\Tag;

use AdnanMula\Cards\Domain\Model\Keyforge\Deck\Tag\Base\KeyforgeTagPositiveTrait;
use AdnanMula\Cards\Domain\Model\Shared\ValueObject\Locale;
use AdnanMula\Cards\Domain\Model\Shared\ValueObject\Uuid;
use AdnanMula\Cards\Shared\LocalizedString;

final class KeyforgeTagSynergyHigh extends KeyforgeTagPositiveTrait
{
    public function __construct()
    {
        parent::__construct(
            Uuid::from('a73fd6c3-53f8-41bc-b1b0-b8fbe63e676d'),
            LocalizedString::fromArray(
                [
                    Locale::es_ES->value => 'Sinérgico',
                    Locale::en_GB->value => 'High synergy',
                ],
            ),
        );
    }
}
