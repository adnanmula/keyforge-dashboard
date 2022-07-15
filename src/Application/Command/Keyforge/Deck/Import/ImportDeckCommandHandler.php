<?php declare(strict_types=1);

namespace AdnanMula\Cards\Application\Command\Keyforge\Deck\Import;

use AdnanMula\Cards\Domain\Model\Keyforge\KeyforgeDeck;
use AdnanMula\Cards\Domain\Model\Keyforge\KeyforgeDeckRepository;
use AdnanMula\Cards\Domain\Model\Keyforge\ValueObject\KeyforgeDeckHouses;
use AdnanMula\Cards\Domain\Model\Keyforge\ValueObject\KeyforgeHouse;
use AdnanMula\Cards\Domain\Model\Keyforge\ValueObject\KeyforgeSet;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Contracts\HttpClient\HttpClientInterface;

final class ImportDeckCommandHandler
{
    public function __construct(
        private KeyforgeDeckRepository $repository,
        private HttpClientInterface $dokClient
    ) {}

    public function __invoke(ImportDeckCommand $command): void
    {
        $deck = $this->repository->byId($command->deckId());

        if (null !== $deck) {
            return;
        }

        $response = $this->dokClient->request(Request::METHOD_GET, '/public-api/v3/decks/' . $command->deckId());
        $deck = $response->toArray();

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
