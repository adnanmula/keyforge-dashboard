<?php declare(strict_types=1);

namespace AdnanMula\Cards\Domain\Model\Keyforge\Deck\Tag;

use AdnanMula\Cards\Domain\Model\Keyforge\Deck\Tag\Base\KeyforgeTagNegativeTrait;
use AdnanMula\Cards\Domain\Model\Shared\ValueObject\Locale;
use AdnanMula\Cards\Domain\Model\Shared\ValueObject\Uuid;
use AdnanMula\Cards\Shared\LocalizedString;

final class KeyforgeTagArtifactControlLow extends KeyforgeTagNegativeTrait
{
    public function __construct()
    {
        parent::__construct(
            Uuid::from('87992af6-e5f8-4b4f-8b4e-5c53153b0f66'),
            LocalizedString::fromArray(
                [
                    Locale::es_ES->value => 'Sin control de artefactos',
                    Locale::en_GB->value => 'No artifact control',
                ],
            ),
        );
    }
}
