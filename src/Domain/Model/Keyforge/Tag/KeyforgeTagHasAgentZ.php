<?php declare(strict_types=1);

namespace AdnanMula\Cards\Domain\Model\Keyforge\Tag;

use AdnanMula\Cards\Domain\Model\Keyforge\Tag\Base\KeyforgeTagHasCard;
use AdnanMula\Cards\Domain\Model\Shared\ValueObject\Uuid;

final class KeyforgeTagHasAgentZ extends KeyforgeTagHasCard
{
    public function __construct()
    {
        parent::__construct(
            Uuid::from('b0c3e776-45f3-4605-af70-3c35933fb36c'),
            'Tiene agente Z',
        );
    }
}
