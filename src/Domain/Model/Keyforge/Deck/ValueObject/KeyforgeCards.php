<?php declare(strict_types=1);

namespace AdnanMula\Cards\Domain\Model\Keyforge\Deck\ValueObject;

final readonly class KeyforgeCards implements \JsonSerializable
{
    // @codingStandardsIgnoreStart
    public const BOARD_CLEARS = ['tectonic-shift', 'phloxem-spike', 'opal-knight', 'general-sherman', 'final-refrain', 'krrrzzzaaap', 'guilty-hearts', 'onyx-knight', 'standardized-testing', 'three-fates', 'strange-gizmo', 'general-order-24', 'earthshaker', 'axiom-of-grisk', 'groundbreaking-discovery', 'selective-preservation', 'kiligogs-trench', 'adult-swim', 'longfused-mines', 'market-crash', 'carpet-phloxem', 'crushing-charge', 'champions-challenge', 'bouncing-deathquark', 'neutron-shark', 'the-spirits-way', 'mlstrom', 'echoing-deathknell', 'election', 'mass-buyout', 'concussive-transfer', 'unlocked-gateway', 'return-to-rubble', 'ammonia-clouds', 'spartasaur', 'poison-wave', 'mind-over-matter', 'grand-alliance-council', 'hebe-the-huge', 'gateway-to-dis', 'mind-bullets', 'hysteria', 'ballcano', 'cowards-end', 'phoenix-heart', 'infighting', 'dark-wave', 'tendrils-of-pain', 'piranha-monkeys', 'harbinger-of-doom', 'skixuno', 'numquid-the-fair', 'key-to-dis', 'final-analysis', 'plan-10', 'plummet', 'midyear-festivities', 'kaboom', 'unnatural-selection', 'plague-wind', 'mberlution', 'savage-clash', 'winds-of-death', 'deescalation', 'war-of-the-worlds', 'ragnarok', 'catch-and-release', 'soul-bomb', 'into-the-warp', 'the-big-one', 'harvest-time', 'extinction', 'dance-of-doom', 'tertiate', 'quintrino-warp', 'gleeful-mayhem', 'quintrino-flux'];
    public const SCALING_AMBER_CONTROL = ['interdimensional-graft', 'doorstep-to-heaven', 'bring-low', 'deusillus', 'ronnie-wristclocks', 'shatter-storm', 'the-first-scroll', 'rant-and-rive', 'submersive-principle', 'martyr-of-the-vault', 'effervescent-principle', 'ant110ny', 'gatekeeper', 'trawler', 'cutthroat-research', 'too-much-to-protect', 'burn-the-stockpile', 'drumble', 'forgemaster-og', 'memorialize-the-fallen', 'closeddoor-negotiation'];
    // @codingStandardsIgnoreEnd

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
            $urlPieces = explode('/', $deck['tokenInfo']['nameUrl']);
            $serializedName = explode('.', end($urlPieces))[0];

            $extraCards[] = [
                'name' => $deck['tokenInfo']['name'],
                'serializedName' => $serializedName,
                'type' => 'token-creature',
                'imageUrl' => $deck['tokenInfo']['nameUrl'],
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
                $count++;
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
}
