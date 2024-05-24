<?php declare(strict_types=1);

namespace AdnanMula\Cards\Domain\Model\Keyforge\Deck\Tag;

use AdnanMula\Cards\Domain\Model\Keyforge\Deck\Tag\Base\KeyforgeTagNegativeTrait;
use AdnanMula\Cards\Domain\Model\Shared\ValueObject\Locale;
use AdnanMula\Cards\Domain\Model\Shared\ValueObject\Uuid;
use AdnanMula\Cards\Shared\LocalizedString;

final class KeyforgeTagAmberControlLow extends KeyforgeTagNegativeTrait
{
    public function __construct()
    {
        parent::__construct(
            Uuid::from('7a1a95ac-8309-4229-a841-47e2dd36ba83'),
            LocalizedString::fromArray(
                [
                    Locale::es_ES->value => 'Control de ambar bajo',
                    Locale::en_GB->value => 'Low amber control',
                ],
            ),
        );
    }
}
