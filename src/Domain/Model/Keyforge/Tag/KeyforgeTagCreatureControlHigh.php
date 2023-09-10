<?php declare(strict_types=1);

namespace AdnanMula\Cards\Domain\Model\Keyforge\Tag;

use AdnanMula\Cards\Domain\Model\Keyforge\Tag\Base\KeyforgeTagPositiveTrait;
use AdnanMula\Cards\Domain\Model\Shared\ValueObject\Locale;
use AdnanMula\Cards\Domain\Model\Shared\ValueObject\Uuid;
use AdnanMula\Cards\Shared\LocalizedString;

final class KeyforgeTagCreatureControlHigh extends KeyforgeTagPositiveTrait
{
    public function __construct()
    {
        parent::__construct(
            Uuid::from('3b460b78-498a-469b-995b-cd0157672cc6'),
            LocalizedString::fromArray(
                [
                    Locale::es_ES->value => 'Control de criaturas alto',
                    Locale::en_GB->value => 'High creature control',
                ],
            ),
        );
    }
}
