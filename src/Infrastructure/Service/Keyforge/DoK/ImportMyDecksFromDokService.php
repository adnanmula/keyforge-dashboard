<?php declare(strict_types=1);

namespace AdnanMula\Cards\Infrastructure\Service\Keyforge\DoK;

use AdnanMula\Cards\Domain\Model\Keyforge\KeyforgeDeck;
use AdnanMula\Cards\Domain\Model\Keyforge\KeyforgeDeckRepository;
use AdnanMula\Cards\Domain\Model\Keyforge\ValueObject\KeyforgeDeckHouses;
use AdnanMula\Cards\Domain\Model\Keyforge\ValueObject\KeyforgeHouse;
use AdnanMula\Cards\Domain\Model\Keyforge\ValueObject\KeyforgeSet;
use AdnanMula\Cards\Domain\Model\Shared\ValueObject\Uuid;
use AdnanMula\Cards\Domain\Service\Keyforge\Deck\DeckApplyPredefinedTagsService;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Contracts\HttpClient\HttpClientInterface;

final class ImportMyDecksFromDokService
{
    public function __construct(
        private HttpClientInterface $dokClient,
        private KeyforgeDeckRepository $repository,
        private DeckApplyPredefinedTagsService $tagsService,
    ) {}

    public function execute(string $token, Uuid $owner, bool $forceUpdate = false): void
    {
        try {
            $response = $this->dokClient->request(
                Request::METHOD_GET,
                '/public-api/v1/my-decks',
                ['headers' => ['Api-Key' => $token]],
            )->toArray();
        } catch (\Throwable) {
            throw new \Exception('Error desconocido');
        }

        $deckIds = \array_map(static fn (array $deck): Uuid => Uuid::from($deck['deck']['keyforgeId']), $response);
        $storedDecks = $this->repository->byIds(...$deckIds);

        foreach ($response as $responseDeck) {
            /** @var ?KeyforgeDeck $storedDeck */
            $storedDeck = \array_values(\array_filter(
                $storedDecks,
                static fn (KeyforgeDeck $d) => $d->id()->value() === $responseDeck['deck']['keyforgeId'],
            ))[0] ?? null;

            if (false === $forceUpdate && null !== $storedDeck) {
                continue;
            }

            $houses = \array_map(static fn (array $data) => $data['house'], $responseDeck['deck']['housesAndCards']);

            $newDeck = new KeyforgeDeck(
                Uuid::from($responseDeck['deck']['keyforgeId']),
                $responseDeck['deck']['name'],
                KeyforgeSet::fromDokName($responseDeck['deck']['expansion']),
                KeyforgeDeckHouses::from(
                    KeyforgeHouse::fromDokName($houses[0]),
                    KeyforgeHouse::fromDokName($houses[1]),
                    KeyforgeHouse::fromDokName($houses[2]),
                ),
                $responseDeck['deck']['sasRating'],
                null === $storedDeck ? 0 : $storedDeck->wins(),
                null === $storedDeck ? 0 : $storedDeck->losses(),
                $responseDeck,
                $owner,
                null === $storedDeck ? '' : $storedDeck->notes(),
                [],
            );

            $this->repository->save($newDeck);
            $this->tagsService->execute($newDeck);
        }
    }
}
