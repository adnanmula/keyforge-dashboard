<?php declare(strict_types=1);

namespace AdnanMula\Cards\Domain\Model\Keyforge\Deck\Tag;

use AdnanMula\Cards\Domain\Model\Keyforge\Deck\Tag\Base\KeyforgeTagPositiveTrait;
use AdnanMula\Cards\Domain\Model\Shared\ValueObject\Locale;
use AdnanMula\Cards\Domain\Model\Shared\ValueObject\Uuid;
use AdnanMula\Cards\Shared\LocalizedString;

final class KeyforgeTagCreatureProtectionHigh extends KeyforgeTagPositiveTrait
{
    public function __construct()
    {
        parent::__construct(
            Uuid::from('7c169d7d-0070-4e5b-a1fe-c204140548ef'),
            LocalizedString::fromArray(
                [
                    Locale::es_ES->value => 'Protección de criaturas alta',
                    Locale::en_GB->value => 'High creature protection',
                ],
            ),
        );
    }
}
