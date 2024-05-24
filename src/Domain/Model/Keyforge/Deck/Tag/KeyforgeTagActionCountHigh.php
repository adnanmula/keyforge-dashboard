<?php declare(strict_types=1);

namespace AdnanMula\Cards\Domain\Model\Keyforge\Deck\Tag;

use AdnanMula\Cards\Domain\Model\Keyforge\Deck\Tag\Base\KeyforgeTagNeutralTrait;
use AdnanMula\Cards\Domain\Model\Shared\ValueObject\Locale;
use AdnanMula\Cards\Domain\Model\Shared\ValueObject\Uuid;
use AdnanMula\Cards\Shared\LocalizedString;

final class KeyforgeTagActionCountHigh extends KeyforgeTagNeutralTrait
{
    public function __construct()
    {
        parent::__construct(
            Uuid::from('f428f793-118c-499f-af21-2fe2805ef8a2'),
            LocalizedString::fromArray(
                [
                    Locale::es_ES->value => 'Muchas acciones',
                    Locale::en_GB->value => 'High action count',
                ],
            ),
        );
    }
}
