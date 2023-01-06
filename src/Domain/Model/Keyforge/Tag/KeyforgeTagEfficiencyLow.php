<?php declare(strict_types=1);

namespace AdnanMula\Cards\Domain\Model\Keyforge\Tag;

use AdnanMula\Cards\Domain\Model\Keyforge\Tag\Base\KeyforgeTagNegativeTrait;
use AdnanMula\Cards\Domain\Model\Shared\ValueObject\Uuid;

final class KeyforgeTagEfficiencyLow extends KeyforgeTagNegativeTrait
{
    public function __construct()
    {
        parent::__construct(
            Uuid::from('c1503b8e-7982-4c91-88c5-eac2040fd8fb'),
            'Eficiencia baja',
        );
    }
}
