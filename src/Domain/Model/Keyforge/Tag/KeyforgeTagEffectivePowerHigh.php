<?php declare(strict_types=1);

namespace AdnanMula\Cards\Domain\Model\Keyforge\Tag;

use AdnanMula\Cards\Domain\Model\Keyforge\Tag\Base\KeyforgeTagPositiveTrait;
use AdnanMula\Cards\Domain\Model\Shared\ValueObject\Locale;
use AdnanMula\Cards\Domain\Model\Shared\ValueObject\Uuid;
use AdnanMula\Cards\Shared\LocalizedString;

final class KeyforgeTagEffectivePowerHigh extends KeyforgeTagPositiveTrait
{
    public function __construct()
    {
        parent::__construct(
            Uuid::from('d6971b4e-632c-4234-b2cb-317f3c193c42'),
            LocalizedString::fromArray(
                [
                    Locale::es_ES->value => 'Poder efectivo alto',
                    Locale::en_GB->value => 'High effective power',
                ],
            ),
        );
    }
}
