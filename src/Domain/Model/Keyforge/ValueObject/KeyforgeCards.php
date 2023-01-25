<?php declare(strict_types=1);

namespace AdnanMula\Cards\Domain\Model\Keyforge\ValueObject;

final readonly class KeyforgeCards implements \JsonSerializable
{
    /**
     * @param array<KeyforgeCard> $firstPodCards
     * @param array<KeyforgeCard> $secondPodCards
     * @param array<KeyforgeCard> $thirdPodCards
     */
    private function __construct(
        public KeyforgeHouse $firstPodHouse,
        public array $firstPodCards,
        public KeyforgeHouse $secondPodHouse,
        public array $secondPodCards,
        public KeyforgeHouse $thirdPodHouse,
        public array $thirdPodCards,
    ) {}

    public static function fromDokData(array $data): self
    {
        $data = $data['deck']['housesAndCards'];

        return new self(
            KeyforgeHouse::fromDokName($data[0]['house']),
            \array_map(static fn (array $card): KeyforgeCard => KeyforgeCard::fromDokData($card), $data[0]['cards']),
            KeyforgeHouse::fromDokName($data[1]['house']),
            \array_map(static fn (array $card): KeyforgeCard => KeyforgeCard::fromDokData($card), $data[1]['cards']),
            KeyforgeHouse::fromDokName($data[2]['house']),
            \array_map(static fn (array $card): KeyforgeCard => KeyforgeCard::fromDokData($card), $data[2]['cards']),
        );
    }

    public function jsonSerialize(): array
    {
        return [
            'firstPodHouse' => $this->firstPodHouse,
            'firstPodCards' => \array_map(static fn (KeyforgeCard $card) => $card->jsonSerialize(), $this->firstPodCards),
            'secondPodHouse' => $this->secondPodHouse,
            'secondPodCards' => \array_map(static fn (KeyforgeCard $card) => $card->jsonSerialize(), $this->secondPodCards),
            'thirdPodHouse' => $this->thirdPodHouse,
            'thirdPodCards' => \array_map(static fn (KeyforgeCard $card) => $card->jsonSerialize(), $this->thirdPodCards),
        ];
    }

    public function has(array $cards): bool
    {
        $deckCards = \array_merge($this->firstPodCards, $this->secondPodCards, $this->thirdPodCards);

        foreach ($deckCards as $card) {
            if (\in_array($card->name, $cards, true)) {
                return true;
            }
        }

        return false;
    }

    public function get(string $card): ?KeyforgeCard
    {
        $deckCards = \array_merge($this->firstPodCards, $this->secondPodCards, $this->thirdPodCards);

        foreach ($deckCards as $deckCard) {
            if ($deckCard->name === $card) {
                return $deckCard;
            }
        }

        return null;
    }
}
