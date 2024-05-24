<?php declare(strict_types=1);

namespace AdnanMula\Cards\Domain\Model\Keyforge\Deck\Tag;

use AdnanMula\Cards\Domain\Model\Keyforge\Deck\Tag\Base\KeyforgeTagPositiveTrait;
use AdnanMula\Cards\Domain\Model\Shared\ValueObject\Locale;
use AdnanMula\Cards\Domain\Model\Shared\ValueObject\Uuid;
use AdnanMula\Cards\Shared\LocalizedString;

final class KeyforgeTagHasScalingAmberControl extends KeyforgeTagPositiveTrait
{
    public function __construct()
    {
        parent::__construct(
            Uuid::from('fa921c9c-1d0d-482a-9462-6767b2c0a080'),
            LocalizedString::fromArray(
                [
                    Locale::es_ES->value => 'Tiene control de ambar escalado',
                    Locale::en_GB->value => 'Has scaling amber control',
                ],
            ),
        );
    }
}
