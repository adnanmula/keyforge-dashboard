<?php declare(strict_types=1);

namespace AdnanMula\Cards\Domain\Model\Keyforge\Deck\Tag;

use AdnanMula\Cards\Domain\Model\Keyforge\Deck\Tag\Base\KeyforgeTagNegativeTrait;
use AdnanMula\Cards\Domain\Model\Shared\ValueObject\Locale;
use AdnanMula\Cards\Domain\Model\Shared\ValueObject\Uuid;
use AdnanMula\Cards\Shared\LocalizedString;

final class KeyforgeTagAmberExpectedLow extends KeyforgeTagNegativeTrait
{
    public function __construct()
    {
        parent::__construct(
            Uuid::from('e3c1285d-fead-4c93-90d9-43019833c4c7'),
            LocalizedString::fromArray(
                [
                    Locale::es_ES->value => 'Ambar esperado bajo',
                    Locale::en_GB->value => 'Low expected amber',
                ],
            ),
        );
    }
}
