<?php declare(strict_types=1);

namespace AdnanMula\Cards\Domain\Model\Keyforge\Tag;

use AdnanMula\Cards\Domain\Model\Keyforge\Tag\Base\KeyforgeTagNegativeTrait;
use AdnanMula\Cards\Domain\Model\Shared\ValueObject\Uuid;

final class KeyforgeTagAmberExpectedLow extends KeyforgeTagNegativeTrait
{
    public function __construct()
    {
        parent::__construct(
            Uuid::from('e3c1285d-fead-4c93-90d9-43019833c4c7'),
            'Ambar esperado bajo',
        );
    }
}
