<?php declare(strict_types=1);

namespace AdnanMula\Cards\Application\Keyforge\Import;

use AdnanMula\Cards\Domain\Model\Keyforge\KeyforgeDeck;
use AdnanMula\Cards\Domain\Model\Keyforge\KeyforgeRepository;
use AdnanMula\Cards\Domain\Model\Keyforge\ValueObject\KeyforgeDeckHouses;
use AdnanMula\Cards\Domain\Model\Keyforge\ValueObject\KeyforgeHouse;
use AdnanMula\Cards\Domain\Model\Keyforge\ValueObject\KeyforgeSet;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Contracts\HttpClient\HttpClientInterface;

final class ImportKeyforgeDeckCommandHandler
{
    public function __construct(
        private KeyforgeRepository $repository,
        private HttpClientInterface $dokClient
    ) {}

    public function __invoke(ImportKeyforgeDeckCommand $command): void
    {
        $deck = $this->repository->byId($command->deckId());

        if (null !== $deck) {
            return;
        }

        $response = $this->dokClient->request(Request::METHOD_GET, '/public-api/v3/decks/' . $command->deckId());
        $deck = $response->toArray();

//        $deck = \json_decode('{"deck":{"id":1687443,"keyforgeId":"10ff6ac7-c6c9-444b-a1aa-10fe87c3c524","expansion":"CALL_OF_THE_ARCHONS","name":"Parker la Sedienta","creatureCount":14,"actionCount":20,"upgradeCount":2,"expectedAmber":30.000000000000004,"amberControl":9.7,"creatureControl":3.325,"efficiency":16.35,"recursion":1.21875,"effectivePower":50,"disruption":1,"aercScore":60,"previousSasRating":70,"previousMajorSasRating":69,"aercVersion":42,"sasRating":71,"synergyRating":14,"antisynergyRating":1,"metaScores":[],"efficiencyBonus":0,"totalPower":40,"cardDrawCount":4,"cardArchiveCount":2,"rawAmber":15,"lastSasUpdate":"2021-12-03","sasPercentile":86.77955322654233,"housesAndCards":[{"house":"Logos","cards":[{"cardTitle":"Dimension Door","rarity":"Uncommon","legacy":false,"maverick":false,"anomaly":false},{"cardTitle":"Interdimensional Graft","rarity":"Uncommon","legacy":false,"maverick":false,"anomaly":false},{"cardTitle":"Library Access","rarity":"Common","legacy":false,"maverick":false,"anomaly":false},{"cardTitle":"Neuro Syphon","rarity":"Uncommon","legacy":false,"maverick":false,"anomaly":false},{"cardTitle":"Neuro Syphon","rarity":"Uncommon","legacy":false,"maverick":false,"anomaly":false},{"cardTitle":"Phase Shift","rarity":"Common","legacy":false,"maverick":false,"anomaly":false},{"cardTitle":"Phase Shift","rarity":"Common","legacy":false,"maverick":false,"anomaly":false},{"cardTitle":"Sloppy Labwork","rarity":"Uncommon","legacy":false,"maverick":false,"anomaly":false},{"cardTitle":"Wild Wormhole","rarity":"Common","legacy":false,"maverick":false,"anomaly":false},{"cardTitle":"Batdrone","rarity":"Common","legacy":false,"maverick":false,"anomaly":false},{"cardTitle":"Doc Bookton","rarity":"Common","legacy":false,"maverick":false,"anomaly":false},{"cardTitle":"Dysania","rarity":"Rare","legacy":false,"maverick":false,"anomaly":false}]},{"house":"Shadows","cards":[{"cardTitle":"Bait and Switch","rarity":"Common","legacy":false,"maverick":false,"anomaly":false},{"cardTitle":"Ghostly Hand","rarity":"Common","legacy":false,"maverick":false,"anomaly":false},{"cardTitle":"Ghostly Hand","rarity":"Common","legacy":false,"maverick":false,"anomaly":false},{"cardTitle":"Hidden Stash","rarity":"Uncommon","legacy":false,"maverick":false,"anomaly":false},{"cardTitle":"Nerve Blast","rarity":"Common","legacy":false,"maverick":false,"anomaly":false},{"cardTitle":"One Last Job","rarity":"Rare","legacy":false,"maverick":false,"anomaly":false},{"cardTitle":"Poison Wave","rarity":"Common","legacy":false,"maverick":false,"anomaly":false},{"cardTitle":"Bad Penny","rarity":"Common","legacy":false,"maverick":false,"anomaly":false},{"cardTitle":"Macis Asp","rarity":"Uncommon","legacy":false,"maverick":false,"anomaly":false},{"cardTitle":"Umbra","rarity":"Common","legacy":false,"maverick":false,"anomaly":false},{"cardTitle":"Urchin","rarity":"Common","legacy":false,"maverick":false,"anomaly":false},{"cardTitle":"Duskrunner","rarity":"Uncommon","legacy":false,"maverick":false,"anomaly":false}]},{"house":"Untamed","cards":[{"cardTitle":"Full Moon","rarity":"Common","legacy":false,"maverick":false,"anomaly":false},{"cardTitle":"Regrowth","rarity":"Common","legacy":false,"maverick":false,"anomaly":false},{"cardTitle":"Save the Pack","rarity":"Common","legacy":false,"maverick":false,"anomaly":false},{"cardTitle":"Vigor","rarity":"Common","legacy":false,"maverick":false,"anomaly":false},{"cardTitle":"Dew Faerie","rarity":"Common","legacy":false,"maverick":false,"anomaly":false},{"cardTitle":"Halacor","rarity":"Uncommon","legacy":false,"maverick":false,"anomaly":false},{"cardTitle":"Hunting Witch","rarity":"Common","legacy":false,"maverick":false,"anomaly":false},{"cardTitle":"Murmook","rarity":"Common","legacy":false,"maverick":false,"anomaly":false},{"cardTitle":"Niffle Ape","rarity":"Common","legacy":false,"maverick":false,"anomaly":false},{"cardTitle":"Snufflegator","rarity":"Common","legacy":false,"maverick":false,"anomaly":false},{"cardTitle":"Witch of the Wilds","rarity":"Rare","legacy":false,"maverick":false,"anomaly":false},{"cardTitle":"Way of the Bear","rarity":"Uncommon","legacy":false,"maverick":false,"anomaly":false}]}],"dateAdded":"2020-01-21"},"sasVersion":42}', true, 512, JSON_THROW_ON_ERROR);

        $houses = \array_map(static fn (array $data) => $data['house'], $deck['deck']['housesAndCards']);

        $this->repository->save(
            new KeyforgeDeck(
                $command->deckId(),
                $deck['deck']['name'],
                KeyforgeSet::fromDokName($deck['deck']['expansion']),
                KeyforgeDeckHouses::from(
                    KeyforgeHouse::fromDokName($houses[0]),
                    KeyforgeHouse::fromDokName($houses[1]),
                    KeyforgeHouse::fromDokName($houses[2]),
                ),
                $deck['deck']['sasRating'],
                0,
                0,
                $deck,
            ),
        );
    }
}
