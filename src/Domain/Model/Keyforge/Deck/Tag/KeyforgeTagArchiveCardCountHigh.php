<?php declare(strict_types=1);

namespace AdnanMula\Cards\Domain\Model\Keyforge\Deck\Tag;

use AdnanMula\Cards\Domain\Model\Keyforge\Deck\Tag\Base\KeyforgeTagPositiveTrait;
use AdnanMula\Cards\Domain\Model\Shared\ValueObject\Locale;
use AdnanMula\Cards\Domain\Model\Shared\ValueObject\Uuid;
use AdnanMula\Cards\Shared\LocalizedString;

final class KeyforgeTagArchiveCardCountHigh extends KeyforgeTagPositiveTrait
{
    public function __construct()
    {
        parent::__construct(
            Uuid::from('f3170465-0e67-44bb-b902-c5f9731b29e8'),
            LocalizedString::fromArray(
                [
                    Locale::es_ES->value => 'Archiva mucho',
                    Locale::en_GB->value => 'High archives',
                ],
            ),
        );
    }
}
