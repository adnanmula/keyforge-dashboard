<?php declare(strict_types=1);

namespace AdnanMula\Cards\Application\Command\Keyforge\Deck\GenerateAlliances;

use AdnanMula\Cards\Domain\Model\Keyforge\Deck\ValueObject\KeyforgeHouse;
use Assert\Assert;

final readonly class GenerateDeckAlliancesCommand
{
    private(set) array $decks;
    private(set) bool $addToMyDecks;
    private(set) bool $addToOwnedDok;

    public function __construct($decks, $addToMyDecks, $addToOwnedDok)
    {
        Assert::lazy()
            ->that($decks, 'decks')->all()->isArray()
            ->that($addToMyDecks, 'addToMyDecks')->boolean()
            ->that($addToOwnedDok, 'addToOwnedDok')->boolean()
            ->verifyNow();

        foreach ($decks as $id => $houses) {
            Assert::lazy()
                ->that($id)->uuid()
                ->that($houses)->all()->inArray(KeyforgeHouse::values())
                ->verifyNow();

            if (0 === count($houses)) {
                unset($decks[$id]);
            }
        }

        $this->decks = $decks;
        $this->addToMyDecks = $addToMyDecks;
        $this->addToOwnedDok = $addToOwnedDok;
    }

    public function deckIds(): array
    {
        return array_keys($this->decks);
    }

    public function housesOf(string $deckId): array
    {
        return $this->decks[$deckId] ?? [];
    }
}
