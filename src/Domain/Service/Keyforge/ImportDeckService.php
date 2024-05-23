<?php declare(strict_types=1);

namespace AdnanMula\Cards\Domain\Service\Keyforge;

use AdnanMula\Cards\Domain\Model\Keyforge\KeyforgeDeck;
use AdnanMula\Cards\Domain\Model\Shared\ValueObject\Uuid;

interface ImportDeckService
{
    public function execute(Uuid $uuid, ?Uuid $owner = null, bool $forceUpdate = false, bool $withHistory = true): ?KeyforgeDeck;
}
