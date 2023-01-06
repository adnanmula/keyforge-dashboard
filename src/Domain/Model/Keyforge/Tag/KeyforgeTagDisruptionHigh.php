<?php declare(strict_types=1);

namespace AdnanMula\Cards\Domain\Model\Keyforge\Tag;

use AdnanMula\Cards\Domain\Model\Keyforge\Tag\Base\KeyforgeTagPositiveTrait;
use AdnanMula\Cards\Domain\Model\Shared\ValueObject\Uuid;

final class KeyforgeTagDisruptionHigh extends KeyforgeTagPositiveTrait
{
    public function __construct()
    {
        parent::__construct(
            Uuid::from('0cf47bfc-df85-4459-b6f1-8fec291a614d'),
            'Disrupcion alta',
        );
    }
}
