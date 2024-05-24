<?php declare(strict_types=1);

namespace AdnanMula\Cards\Domain\Model\Keyforge\Deck\Tag;

use AdnanMula\Cards\Domain\Model\Keyforge\Deck\Tag\Base\KeyforgeTagNegativeTrait;
use AdnanMula\Cards\Domain\Model\Shared\ValueObject\Locale;
use AdnanMula\Cards\Domain\Model\Shared\ValueObject\Uuid;
use AdnanMula\Cards\Shared\LocalizedString;

final class KeyforgeTagEfficiencyLow extends KeyforgeTagNegativeTrait
{
    public function __construct()
    {
        parent::__construct(
            Uuid::from('c1503b8e-7982-4c91-88c5-eac2040fd8fb'),
            LocalizedString::fromArray(
                [
                    Locale::es_ES->value => 'Eficiencia baja',
                    Locale::en_GB->value => 'Low efficiency',
                ],
            ),
        );
    }
}
