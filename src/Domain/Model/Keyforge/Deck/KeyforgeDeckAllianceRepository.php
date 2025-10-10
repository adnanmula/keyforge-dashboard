<?php declare(strict_types=1);

namespace AdnanMula\Cards\Domain\Model\Keyforge\Deck;

use AdnanMula\Cards\Domain\Model\Shared\ValueObject\Uuid;

interface KeyforgeDeckAllianceRepository
{
    public function saveComposition(Uuid $id, array $composition): void;
    public function isAlreadyImported(string $id1, string $house1, string $id2, string $house2, string $id3, string $house3, string $extraCardType, string $extraCard): ?string;
}
