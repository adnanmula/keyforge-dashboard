<?php declare(strict_types=1);

namespace AdnanMula\Cards\Infrastructure\Service\Keyforge\DoK;

use AdnanMula\Cards\Domain\Model\Keyforge\Deck\Exception\DeckNotExistsException;
use AdnanMula\Cards\Domain\Model\Keyforge\Deck\KeyforgeDeck;
use AdnanMula\Cards\Domain\Model\Keyforge\Deck\KeyforgeDeckRepository;
use AdnanMula\Cards\Domain\Model\Keyforge\Deck\ValueObject\KeyforgeDeckData;
use AdnanMula\Cards\Domain\Model\Keyforge\Deck\ValueObject\KeyforgeDeckUserData;
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
        private ImportDeckStatHistoryFromDokService $statHistoryService,
    ) {}

    public function execute(Uuid $uuid, ?Uuid $owner = null, bool $forceUpdate = false, bool $withHistory = true, bool $withTags = true): ?KeyforgeDeck
    {
        $isImported = $this->repository->isImported($uuid);

        if (false === $forceUpdate && $isImported) {
            return $this->repository->byId($uuid);
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
            KeyforgeDeckUserData::from(Uuid::from($deck['deck']['keyforgeId']), $owner, 0, 0, ''),
        );

        $this->repository->save($newDeck, false);
        $this->repository->saveDeckData($newDeck->data());

        if (false === $isImported) {
            $this->repository->saveDeckUserData($newDeck->userData());
        }

        if ($withHistory) {
            $this->statHistoryService->execute($newDeck->id());
        }

        if ($withTags) {
            $this->tagsService->execute($newDeck);
        }

        return $newDeck;
    }
}
