<?php declare(strict_types=1);

namespace AdnanMula\Cards\Domain\Model\Keyforge\Tag;

use AdnanMula\Cards\Domain\Model\Keyforge\Tag\Base\KeyforgeTagNeutralTrait;
use AdnanMula\Cards\Domain\Model\Shared\ValueObject\Uuid;

final class KeyforgeTagArtifactCountHigh extends KeyforgeTagNeutralTrait
{
    public function __construct()
    {
        parent::__construct(
            Uuid::from('75593d1c-62b3-474e-8d65-8d7f3d3966a9'),
            'Muchos artefactos',
        );
    }
}
