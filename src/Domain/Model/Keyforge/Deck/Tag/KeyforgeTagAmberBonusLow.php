<?php declare(strict_types=1);

namespace AdnanMula\Cards\Domain\Model\Keyforge\Deck\Tag;

use AdnanMula\Cards\Domain\Model\Keyforge\Deck\Tag\Base\KeyforgeTagNegativeTrait;
use AdnanMula\Cards\Domain\Model\Shared\ValueObject\Locale;
use AdnanMula\Cards\Domain\Model\Shared\ValueObject\Uuid;
use AdnanMula\Cards\Shared\LocalizedString;

final class KeyforgeTagAmberBonusLow extends KeyforgeTagNegativeTrait
{
    public function __construct()
    {
        parent::__construct(
            Uuid::from('089eb40d-82ca-4ef3-ad7e-252c39ed6829'),
            LocalizedString::fromArray(
                [
                    Locale::es_ES->value => 'Pocos pimpollos de ambar',
                    Locale::en_GB->value => 'Low amber pip count',
                ],
            ),
        );
    }
}
