<?php declare(strict_types=1);

namespace AdnanMula\Cards\Domain\Model\Keyforge\Deck\Tag;

use AdnanMula\Cards\Domain\Model\Keyforge\Deck\Tag\Base\KeyforgeTagHasCard;
use AdnanMula\Cards\Domain\Model\Shared\ValueObject\Locale;
use AdnanMula\Cards\Domain\Model\Shared\ValueObject\Uuid;
use AdnanMula\Cards\Shared\LocalizedString;

final class KeyforgeTagHasAnomaly extends KeyforgeTagHasCard
{
    public function __construct()
    {
        parent::__construct(
            Uuid::from('61fb4a66-e417-48e6-a27e-41d61ddff03d'),
            LocalizedString::fromArray(
                [
                    Locale::es_ES->value => 'Tiene anomalía',
                    Locale::en_GB->value => 'Has anomaly',
                ],
            ),
        );
    }
}
