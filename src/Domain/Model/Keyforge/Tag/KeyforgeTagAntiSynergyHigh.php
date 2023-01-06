<?php declare(strict_types=1);

namespace AdnanMula\Cards\Domain\Model\Keyforge\Tag;

use AdnanMula\Cards\Domain\Model\Keyforge\Tag\Base\KeyforgeTagNegativeTrait;
use AdnanMula\Cards\Domain\Model\Shared\ValueObject\Uuid;

final class KeyforgeTagAntiSynergyHigh extends KeyforgeTagNegativeTrait
{
    public function __construct()
    {
        parent::__construct(
            Uuid::from('d82f01fd-a505-4256-86c2-8f07b64e6ecf'),
            'Antisinérgico',
        );
    }
}
