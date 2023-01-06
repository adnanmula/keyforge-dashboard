<?php declare(strict_types=1);

namespace AdnanMula\Cards\Domain\Model\Keyforge\Tag;

use AdnanMula\Cards\Domain\Model\Keyforge\Tag\Base\KeyforgeTagPositiveTrait;
use AdnanMula\Cards\Domain\Model\Shared\ValueObject\Uuid;

final class KeyforgeTagHasScalingAmberControl extends KeyforgeTagPositiveTrait
{
    public function __construct()
    {
        parent::__construct(
            Uuid::from('fa921c9c-1d0d-482a-9462-6767b2c0a080'),
            'Tiene control de ambar escalado',
        );
    }
}
