<?php declare(strict_types=1);

namespace AdnanMula\Cards\Domain\Model\Keyforge\Tag;

use AdnanMula\Cards\Domain\Model\Keyforge\Tag\Base\KeyforgeTagPositiveTrait;
use AdnanMula\Cards\Domain\Model\Shared\ValueObject\Locale;
use AdnanMula\Cards\Domain\Model\Shared\ValueObject\Uuid;
use AdnanMula\Cards\Shared\LocalizedString;

final class KeyforgeTagAmberExpectedHigh extends KeyforgeTagPositiveTrait
{
    public function __construct()
    {
        parent::__construct(
            Uuid::from('81f44ea4-0ee5-4221-b2a7-9211a2806afe'),
            LocalizedString::fromArray(
                [
                    Locale::es_ES->value => 'Ambar esperado alto',
                    Locale::en_GB->value => 'High expected amber',
                ],
            ),
        );
    }
}
