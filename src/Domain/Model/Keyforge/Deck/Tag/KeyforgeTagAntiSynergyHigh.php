<?php declare(strict_types=1);

namespace AdnanMula\Cards\Domain\Model\Keyforge\Deck\Tag;

use AdnanMula\Cards\Domain\Model\Keyforge\Deck\Tag\Base\KeyforgeTagNegativeTrait;
use AdnanMula\Cards\Domain\Model\Shared\ValueObject\Locale;
use AdnanMula\Cards\Domain\Model\Shared\ValueObject\Uuid;
use AdnanMula\Cards\Shared\LocalizedString;

final class KeyforgeTagAntiSynergyHigh extends KeyforgeTagNegativeTrait
{
    public function __construct()
    {
        parent::__construct(
            Uuid::from('d82f01fd-a505-4256-86c2-8f07b64e6ecf'),
            LocalizedString::fromArray(
                [
                    Locale::es_ES->value => 'Antisinergia alta',
                    Locale::en_GB->value => 'High antisynergy',
                ],
            ),
        );
    }
}
