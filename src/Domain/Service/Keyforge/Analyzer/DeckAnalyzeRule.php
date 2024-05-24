<?php declare(strict_types=1);

namespace AdnanMula\Cards\Domain\Service\Keyforge\Analyzer;

use AdnanMula\Cards\Domain\Model\Keyforge\Deck\KeyforgeDeck;

interface DeckAnalyzeRule
{
    public function execute(KeyforgeDeck $deck): ?array;
}
