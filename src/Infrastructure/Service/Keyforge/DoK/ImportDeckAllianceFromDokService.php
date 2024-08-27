<?php declare(strict_types=1);

namespace AdnanMula\Cards\Infrastructure\Service\Keyforge\DoK;

use AdnanMula\Cards\Domain\Model\Keyforge\Deck\Exception\DeckNotExistsException;
use AdnanMula\Cards\Domain\Model\Keyforge\Deck\KeyforgeDeck;
use AdnanMula\Cards\Domain\Model\Keyforge\Deck\KeyforgeDeckRepository;
use AdnanMula\Cards\Domain\Model\Keyforge\Deck\ValueObject\KeyforgeCards;
use AdnanMula\Cards\Domain\Model\Keyforge\Deck\ValueObject\KeyforgeDeckHouses;
use AdnanMula\Cards\Domain\Model\Keyforge\Deck\ValueObject\KeyforgeDeckStats;
use AdnanMula\Cards\Domain\Model\Keyforge\Deck\ValueObject\KeyforgeDeckType;
use AdnanMula\Cards\Domain\Model\Keyforge\Deck\ValueObject\KeyforgeSet;
use AdnanMula\Cards\Domain\Model\Shared\ValueObject\Uuid;
use AdnanMula\Cards\Domain\Service\Keyforge\Deck\DeckApplyPredefinedTagsService;
use AdnanMula\Cards\Domain\Service\Keyforge\ImportDeckAllianceService;
use AdnanMula\Criteria\Criteria;
use AdnanMula\Criteria\Filter\Filter;
use AdnanMula\Criteria\Filter\FilterType;
use AdnanMula\Criteria\FilterField\FilterField;
use AdnanMula\Criteria\FilterGroup\AndFilterGroup;
use AdnanMula\Criteria\FilterValue\FilterOperator;
use AdnanMula\Criteria\FilterValue\StringFilterValue;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Contracts\HttpClient\HttpClientInterface;

final class ImportDeckAllianceFromDokService implements ImportDeckAllianceService
{
    public function __construct(
        private KeyforgeDeckRepository $repository,
        private HttpClientInterface $dokClient,
        private DeckApplyPredefinedTagsService $tagsService,
    ) {}

    public function execute(Uuid $uuid, ?Uuid $owner = null, bool $forceUpdate = false): ?KeyforgeDeck
    {
        $deck = $this->repository->searchOne(new Criteria(
            null,
            null,
            null,
            new AndFilterGroup(
                FilterType::AND,
                new Filter(new FilterField('id'), new StringFilterValue($uuid->value()), FilterOperator::EQUAL),
                new Filter(new FilterField('deck_type'), new StringFilterValue(KeyforgeDeckType::ALLIANCE->value), FilterOperator::EQUAL),
            ),
        ));

        if (false === $forceUpdate && null !== $deck) {
            return $deck;
        }

        try {
            $response = $this->dokClient->request(Request::METHOD_GET, '/public-api/v1/alliance-decks/' . $uuid->value());
        } catch (\Throwable) {
            throw new \Exception('Error desconocido');
        }

        $deckResponse = $response->toArray();

        if ($deckResponse['deck'] === []) {
            throw new DeckNotExistsException();
        }

        $newDeck = new KeyforgeDeck(
            Uuid::from($deckResponse['deck']['keyforgeId']),
            $deckResponse['deck']['id'],
            KeyforgeDeckType::ALLIANCE,
            $deckResponse['deck']['name'],
            KeyforgeSet::fromDokName($deckResponse['deck']['expansion']),
            KeyforgeDeckHouses::fromDokData($deckResponse),
            KeyforgeCards::fromDokData($deckResponse),
            KeyforgeDeckStats::fromDokData($deckResponse),
        );

        $this->repository->save($newDeck);

        if (null !== $owner) {
            $this->repository->addOwner($newDeck->id(), $owner);
        }

        $this->tagsService->execute($newDeck->id());

        return $newDeck;
    }
}
