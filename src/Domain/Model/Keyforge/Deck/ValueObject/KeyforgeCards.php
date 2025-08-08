<?php declare(strict_types=1);

namespace AdnanMula\Cards\Domain\Model\Keyforge\Deck\ValueObject;

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
        public array $extraCards = [],
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            KeyforgeHouse::fromDokName($data['firstPodHouse']),
            \array_map(static fn (array $card): KeyforgeCard => KeyforgeCard::fromArray($card), $data['firstPodCards']),
            KeyforgeHouse::fromDokName($data['secondPodHouse']),
            \array_map(static fn (array $card): KeyforgeCard => KeyforgeCard::fromArray($card), $data['secondPodCards']),
            KeyforgeHouse::fromDokName($data['thirdPodHouse']),
            \array_map(static fn (array $card): KeyforgeCard => KeyforgeCard::fromArray($card), $data['thirdPodCards']),
            $data['extraCards'] ?? [],
        );
    }

    public static function fromDokData(array $data): self
    {
        $deck = $data['deck'];
        $cards = $deck['housesAndCards'];
        $extraCards = [];

        if (\array_key_exists('tokenInfo', $deck)) {
            $serializedName = self::nameFromUrl($deck['tokenInfo']['nameUrl']);

            $extraCards[] = [
                'name' => $deck['tokenInfo']['name'],
                'serializedName' => $serializedName,
                'type' => 'token-creature',
                'imageUrl' => $deck['tokenInfo']['nameUrl'],
            ];
        }

        if (\array_key_exists('prophecies', $deck)) {
            foreach ($deck['prophecies'] as $prophecy) {
                $serializedName = self::nameFromUrl($prophecy['cardTitleUrl']);

                $extraCards[] = [
                    'name' => $prophecy['cardTitle'],
                    'serializedName' => $serializedName,
                    'type' => 'prophecy',
                    'imageUrl' => $prophecy['cardTitleUrl'],
                ];
            }
        }

        if (\array_key_exists('archonPower', $deck)) {
            $archonPowerCard = $deck['archonPower'];

            $serializedName = self::nameFromUrl($archonPowerCard['cardTitleUrl']);

            $extraCards[] = [
                'name' => $archonPowerCard['cardTitle'],
                'serializedName' => $serializedName,
                'type' => 'archon-power',
                'imageUrl' => $archonPowerCard['cardTitleUrl'],
            ];
        }

        return new self(
            KeyforgeHouse::fromDokName($cards[0]['house']),
            \array_map(static fn (array $card): KeyforgeCard => KeyforgeCard::fromDokData($card), $cards[0]['cards']),
            KeyforgeHouse::fromDokName($cards[1]['house']),
            \array_map(static fn (array $card): KeyforgeCard => KeyforgeCard::fromDokData($card), $cards[1]['cards']),
            KeyforgeHouse::fromDokName($cards[2]['house']),
            \array_map(static fn (array $card): KeyforgeCard => KeyforgeCard::fromDokData($card), $cards[2]['cards']),
            $extraCards,
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
            'extraCards' => $this->extraCards,
        ];
    }

    public function has(string $card, int $times = 1): bool
    {
        $deckCards = \array_merge($this->firstPodCards, $this->secondPodCards, $this->thirdPodCards);
        $count = 0;

        foreach ($deckCards as $deckCard) {
            if ($deckCard->name === $card) {
                ++$count;
            }

            if ($count >= $times) {
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

    private static function nameFromUrl(string $url): string
    {
        $urlPieces = explode('/', $url);

        return explode('.', end($urlPieces))[0];
    }
}
