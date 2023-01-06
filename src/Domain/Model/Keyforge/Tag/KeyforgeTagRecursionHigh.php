<?php declare(strict_types=1);

namespace AdnanMula\Cards\Domain\Model\Keyforge\Tag;

use AdnanMula\Cards\Domain\Model\Keyforge\Tag\Base\KeyforgeTagPositiveTrait;
use AdnanMula\Cards\Domain\Model\Shared\ValueObject\Uuid;

final class KeyforgeTagRecursionHigh extends KeyforgeTagPositiveTrait
{
    public function __construct()
    {
        parent::__construct(
            Uuid::from('41534876-931c-4508-ae8b-aa7a08805ff5'),
            'Recursion alta',
        );
    }
}
