<?php declare(strict_types=1);

namespace AdnanMula\Cards\Domain\Model\Keyforge\Tag;

use AdnanMula\Cards\Domain\Model\Keyforge\Tag\Base\KeyforgeTagNeutralTrait;
use AdnanMula\Cards\Domain\Model\Shared\ValueObject\Locale;
use AdnanMula\Cards\Domain\Model\Shared\ValueObject\Uuid;
use AdnanMula\Cards\Shared\LocalizedString;

final class KeyforgeTagArtifactCountHigh extends KeyforgeTagNeutralTrait
{
    public function __construct()
    {
        parent::__construct(
            Uuid::from('75593d1c-62b3-474e-8d65-8d7f3d3966a9'),
            LocalizedString::fromArray(
                [
                    Locale::es_ES->value => 'Muchos artefactos',
                    Locale::en_GB->value => 'High artifact count',
                ],
            ),
        );
    }
}
