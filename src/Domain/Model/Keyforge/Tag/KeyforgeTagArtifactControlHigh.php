<?php declare(strict_types=1);

namespace AdnanMula\Cards\Domain\Model\Keyforge\Tag;

use AdnanMula\Cards\Domain\Model\Keyforge\Tag\Base\KeyforgeTagPositiveTrait;
use AdnanMula\Cards\Domain\Model\Shared\ValueObject\Locale;
use AdnanMula\Cards\Domain\Model\Shared\ValueObject\Uuid;
use AdnanMula\Cards\Shared\LocalizedString;

final class KeyforgeTagArtifactControlHigh extends KeyforgeTagPositiveTrait
{
    public function __construct()
    {
        parent::__construct(
            Uuid::from('280c1682-93aa-40c0-be2e-baa1ec07f010'),
            LocalizedString::fromArray(
                [
                    Locale::es_ES->value => 'Control de artefactos alto',
                    Locale::en_GB->value => 'High artifact control',
                ],
            ),
        );
    }
}
