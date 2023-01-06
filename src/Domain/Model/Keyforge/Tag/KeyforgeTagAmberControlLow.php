<?php declare(strict_types=1);

namespace AdnanMula\Cards\Domain\Model\Keyforge\Tag;

use AdnanMula\Cards\Domain\Model\Keyforge\Tag\Base\KeyforgeTagNegativeTrait;
use AdnanMula\Cards\Domain\Model\Shared\ValueObject\Uuid;

final class KeyforgeTagAmberControlLow extends KeyforgeTagNegativeTrait
{
    public function __construct()
    {
        parent::__construct(
            Uuid::from('7a1a95ac-8309-4229-a841-47e2dd36ba83'),
            'Control de ambar bajo',
        );
    }
}
