<?php declare(strict_types=1);

namespace AdnanMula\Cards\Domain\Model\Keyforge\Tag;

use AdnanMula\Cards\Domain\Model\Keyforge\Tag\Base\KeyforgeTagPositiveTrait;
use AdnanMula\Cards\Domain\Model\Shared\ValueObject\Uuid;

final class KeyforgeTagEfficiencyHigh extends KeyforgeTagPositiveTrait
{
    public function __construct()
    {
        parent::__construct(
            Uuid::from('686357e0-2b5b-4db8-af7e-1de263fb8972'),
            'Eficiencia alta',
        );
    }
}
