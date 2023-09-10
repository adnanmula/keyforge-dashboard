<?php declare(strict_types=1);

namespace AdnanMula\Cards\Domain\Model\Keyforge\Tag;

use AdnanMula\Cards\Domain\Model\Keyforge\Tag\Base\KeyforgeTagPositiveTrait;
use AdnanMula\Cards\Domain\Model\Shared\ValueObject\Locale;
use AdnanMula\Cards\Domain\Model\Shared\ValueObject\Uuid;
use AdnanMula\Cards\Shared\LocalizedString;

final class KeyforgeTagHasKeyCheats extends KeyforgeTagPositiveTrait
{
    public function __construct()
    {
        parent::__construct(
            Uuid::from('708e5ef2-f392-4c25-a08e-9ba1ca7e7725'),
            LocalizedString::fromArray(
                [
                    Locale::es_ES->value => 'Tiene key cheat',
                    Locale::en_GB->value => 'Has key cheat',
                ],
            ),
        );
    }
}
