<?php declare(strict_types=1);

namespace AdnanMula\Cards\Domain\Model\Keyforge\Tag;

use AdnanMula\Cards\Domain\Model\Keyforge\Tag\Base\KeyforgeTagPositiveTrait;
use AdnanMula\Cards\Domain\Model\Shared\ValueObject\Locale;
use AdnanMula\Cards\Domain\Model\Shared\ValueObject\Uuid;
use AdnanMula\Cards\Shared\LocalizedString;

final class KeyforgeTagAmberBonusHigh extends KeyforgeTagPositiveTrait
{
    public function __construct()
    {
        parent::__construct(
            Uuid::from('36f14d8c-aeb4-427c-837a-2bbf9711420e'),
            LocalizedString::fromArray(
                [
                    Locale::es_ES->value => 'Muchos pimpollos de ambar',
                    Locale::en_GB->value => 'High amber pip count',
                ],
            ),
        );
    }
}
