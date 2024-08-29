<?php declare(strict_types=1);

namespace AdnanMula\Cards\Application\Command\Keyforge\Deck\GenerateAlliances;

use AdnanMula\Cards\Domain\Model\Keyforge\Deck\ValueObject\KeyforgeHouse;
use Assert\Assert;

final class GenerateDeckAlliancesCommand
{
    public array $deckIds;
    public array $deckHouses;

    public function __construct($deckIds, $deckHouses)
    {
        Assert::lazy()
            ->that($deckIds, 'deckIds')->all()->uuid()
            ->that($deckHouses, 'deckHouses')->isArray()
            ->verifyNow();

        foreach ($deckHouses as $index => $item) {
            Assert::lazy()
                ->that($index)->uuid()
                ->that($item)->all()->inArray(KeyforgeHouse::values())
                ->verifyNow();
        }

        $this->deckIds = $deckIds;
        $this->deckHouses = $deckHouses;
    }
}
