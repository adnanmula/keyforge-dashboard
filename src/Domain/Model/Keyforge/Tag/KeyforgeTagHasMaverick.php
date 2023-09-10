<?php declare(strict_types=1);

namespace AdnanMula\Cards\Domain\Model\Keyforge\Tag;

use AdnanMula\Cards\Domain\Model\Keyforge\Tag\Base\KeyforgeTagHasCard;
use AdnanMula\Cards\Domain\Model\Shared\ValueObject\Locale;
use AdnanMula\Cards\Domain\Model\Shared\ValueObject\Uuid;
use AdnanMula\Cards\Shared\LocalizedString;

final class KeyforgeTagHasMaverick extends KeyforgeTagHasCard
{
    public function __construct()
    {
        parent::__construct(
            Uuid::from('00ac97d5-ee2b-43df-91de-3bb5af5afda4'),
            LocalizedString::fromArray(
                [
                    Locale::es_ES->value => 'Tiene maverick',
                    Locale::en_GB->value => 'Has maverick',
                ],
            ),
        );
    }
}
