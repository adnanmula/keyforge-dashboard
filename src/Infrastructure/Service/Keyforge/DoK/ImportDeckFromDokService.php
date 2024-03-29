<?php declare(strict_types=1);

namespace AdnanMula\Cards\Infrastructure\Service\Keyforge\DoK;

use AdnanMula\Cards\Domain\Model\Keyforge\KeyforgeDeck;
use AdnanMula\Cards\Domain\Model\Keyforge\KeyforgeDeckRepository;
use AdnanMula\Cards\Domain\Model\Keyforge\ValueObject\KeyforgeDeckHouses;
use AdnanMula\Cards\Domain\Model\Keyforge\ValueObject\KeyforgeHouse;
use AdnanMula\Cards\Domain\Model\Keyforge\ValueObject\KeyforgeSet;
use AdnanMula\Cards\Domain\Model\Shared\ValueObject\Uuid;
use AdnanMula\Cards\Domain\Service\Keyforge\Deck\DeckApplyPredefinedTagsService;
use AdnanMula\Cards\Domain\Service\Keyforge\ImportDeckService;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Contracts\HttpClient\HttpClientInterface;

final class ImportDeckFromDokService implements ImportDeckService
{
    public function __construct(
        private KeyforgeDeckRepository $repository,
        private HttpClientInterface $dokClient,
        private DeckApplyPredefinedTagsService $tagsService,
    ) {}

    public function execute(Uuid $uuid, ?Uuid $owner = null, bool $forceUpdate = false): ?KeyforgeDeck
    {
        $savedDeck = $this->repository->byId($uuid);

        if (false === $forceUpdate && null !== $savedDeck) {
            return $savedDeck;
        }

        try {
            $response = $this->dokClient->request(Request::METHOD_GET, '/public-api/v3/decks/' . $uuid);
        } catch (\Throwable) {
            throw new \Exception('Error desconocido');
        }

        $deck = $response->toArray();

        if ($deck['deck'] === []) {
            throw new \Exception('Baraja no encontrada');
        }

        $houses = \array_map(static fn (array $data) => $data['house'], $deck['deck']['housesAndCards']);

        $newDeck = new KeyforgeDeck(
            $uuid,
            $deck['deck']['name'],
            KeyforgeSet::fromDokName($deck['deck']['expansion']),
            KeyforgeDeckHouses::from(
                KeyforgeHouse::fromDokName($houses[0]),
                KeyforgeHouse::fromDokName($houses[1]),
                KeyforgeHouse::fromDokName($houses[2]),
            ),
            $deck['deck']['sasRating'],
            null === $savedDeck ? 0 : $savedDeck->wins(),
            null === $savedDeck ? 0 : $savedDeck->losses(),
            $deck,
            $owner,
            null === $savedDeck ? '' : $savedDeck->notes(),
            [],
        );

        if ($savedDeck->sas() !== $newDeck->sas()) {
            $newDeck->updateSas($savedDeck->sas());
            $newDeck->updatePrevSas(null);
            $newDeck->updateNewSas($deck['deck']['sasRating']);
        }

        $this->repository->save($newDeck);
        $this->tagsService->execute($newDeck);

        return $newDeck;
    }
}
