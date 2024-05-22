<?php declare(strict_types=1);

namespace AdnanMula\Cards\Infrastructure\Service\Keyforge\DoK;

use AdnanMula\Cards\Domain\Model\Keyforge\KeyforgeDeck;
use AdnanMula\Cards\Domain\Model\Keyforge\KeyforgeDeckRepository;
use AdnanMula\Cards\Domain\Model\Keyforge\ValueObject\KeyforgeDeckData;
use AdnanMula\Cards\Domain\Model\Keyforge\ValueObject\KeyforgeDeckUserData;
use AdnanMula\Cards\Domain\Model\Shared\ValueObject\Uuid;
use AdnanMula\Cards\Domain\Service\Keyforge\Deck\DeckApplyPredefinedTagsService;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Contracts\HttpClient\HttpClientInterface;

final readonly class ImportMyDecksFromDokService
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

            $newDeck = new KeyforgeDeck(
                Uuid::from($responseDeck['deck']['keyforgeId']),
                KeyforgeDeckData::fromDokData($responseDeck),
                KeyforgeDeckUserData::from(
                    Uuid::from($responseDeck['deck']['keyforgeId']),
                    $owner,
                    null === $storedDeck ? 0 : $storedDeck->userData()->wins,
                    null === $storedDeck ? 0 : $storedDeck->userData()->losses,
                    null === $storedDeck ? '' : $storedDeck->userData()->notes,
                ),
            );

            $this->repository->save($newDeck, true);
            $this->tagsService->execute($newDeck);
        }
    }
}
