<?php declare(strict_types=1);

namespace AdnanMula\Cards\Domain\Model\Keyforge\Tag;

use AdnanMula\Cards\Domain\Model\Keyforge\Tag\Base\KeyforgeTagNegativeTrait;
use AdnanMula\Cards\Domain\Model\Shared\ValueObject\Locale;
use AdnanMula\Cards\Domain\Model\Shared\ValueObject\Uuid;
use AdnanMula\Cards\Shared\LocalizedString;

final class KeyforgeTagCreatureControlLow extends KeyforgeTagNegativeTrait
{
    public function __construct()
    {
        parent::__construct(
            Uuid::from('ba01ccdd-f6d6-4d1b-b332-86c507f0d888'),
            LocalizedString::fromArray(
                [
                    Locale::es_ES->value => 'Control de criaturas bajo',
                    Locale::en_GB->value => 'Low creature control',
                ],
            ),
        );
    }
}
