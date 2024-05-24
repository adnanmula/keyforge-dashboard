<?php declare(strict_types=1);

namespace AdnanMula\Cards\Domain\Model\Keyforge\Deck\Tag;

use AdnanMula\Cards\Domain\Model\Keyforge\Deck\Tag\Base\KeyforgeTagPositiveTrait;
use AdnanMula\Cards\Domain\Model\Shared\ValueObject\Locale;
use AdnanMula\Cards\Domain\Model\Shared\ValueObject\Uuid;
use AdnanMula\Cards\Shared\LocalizedString;

final class KeyforgeTagHasBoardWipes extends KeyforgeTagPositiveTrait
{
    public function __construct()
    {
        parent::__construct(
            Uuid::from('c56749d3-cf34-419a-9937-688a1a4ac3bc'),
            LocalizedString::fromArray(
                [
                    Locale::es_ES->value => 'Tiene board wipes',
                    Locale::en_GB->value => 'Has board wipe',
                ],
            ),
        );
    }
}
