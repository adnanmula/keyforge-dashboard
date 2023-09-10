<?php declare(strict_types=1);

namespace AdnanMula\Cards\Domain\Model\Keyforge\Tag;

use AdnanMula\Cards\Domain\Model\Keyforge\Tag\Base\KeyforgeTagPositiveTrait;
use AdnanMula\Cards\Domain\Model\Shared\ValueObject\Locale;
use AdnanMula\Cards\Domain\Model\Shared\ValueObject\Uuid;
use AdnanMula\Cards\Shared\LocalizedString;

final class KeyforgeTagAmberControlHigh extends KeyforgeTagPositiveTrait
{
    public function __construct()
    {
        parent::__construct(
            Uuid::from('8e4aac04-78d7-4c33-88d7-007618dbdabe'),
            LocalizedString::fromArray(
                [
                    Locale::es_ES->value => 'Control de ambar alto',
                    Locale::en_GB->value => 'High amber control',
                ],
            ),
        );
    }
}
