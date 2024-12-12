<?php declare(strict_types=1);

namespace AdnanMula\Cards\Domain\Service\Keyforge;

use AdnanMula\Cards\Domain\Model\Keyforge\Deck\KeyforgeDeck;
use AdnanMula\Cards\Domain\Model\Shared\ValueObject\Uuid;

interface ImportDeckAllianceService
{
    public function execute(Uuid $uuid, ?Uuid $owner = null, bool $forceUpdate = false): KeyforgeDeck;
}
