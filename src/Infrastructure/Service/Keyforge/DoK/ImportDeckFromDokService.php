<?php declare(strict_types=1);

namespace AdnanMula\Cards\Infrastructure\Service\Keyforge\DoK;

use AdnanMula\Cards\Domain\Model\Keyforge\Deck\Exception\DeckNotExistsException;
use AdnanMula\Cards\Domain\Model\Keyforge\KeyforgeDeck;
use AdnanMula\Cards\Domain\Model\Keyforge\KeyforgeDeckRepository;
use AdnanMula\Cards\Domain\Model\Keyforge\ValueObject\KeyforgeDeckData;
use AdnanMula\Cards\Domain\Model\Keyforge\ValueObject\KeyforgeDeckUserData;
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
            throw new DeckNotExistsException();
        }

        $newDeck = new KeyforgeDeck(
            Uuid::from($deck['deck']['keyforgeId']),
            KeyforgeDeckData::fromDokData($deck),
            KeyforgeDeckUserData::from(
                Uuid::from($deck['deck']['keyforgeId']),
                $owner,
                null === $savedDeck ? 0 : $savedDeck->userData()->wins,
                null === $savedDeck ? 0 : $savedDeck->userData()->losses,
                null === $savedDeck ? '' : $savedDeck->userData()->notes,
            ),
        );

        $this->repository->save($newDeck, false);
        $this->repository->saveDeckData($newDeck->data());
        $this->tagsService->execute($newDeck);

        return $newDeck;
    }
}
