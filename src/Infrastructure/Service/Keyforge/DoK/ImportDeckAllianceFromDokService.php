<?php declare(strict_types=1);

namespace AdnanMula\Cards\Infrastructure\Service\Keyforge\DoK;

use AdnanMula\Cards\Domain\Model\Keyforge\Card\KeyforgeCard;
use AdnanMula\Cards\Domain\Model\Keyforge\Card\KeyforgeCardRepository;
use AdnanMula\Cards\Domain\Model\Keyforge\Deck\Exception\DeckNotExistsException;
use AdnanMula\Cards\Domain\Model\Keyforge\Deck\KeyforgeDeck;
use AdnanMula\Cards\Domain\Model\Keyforge\Deck\KeyforgeDeckAllianceRepository;
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
use AdnanMula\Criteria\Filter\FilterOperator;
use AdnanMula\Criteria\Filter\Filters;
use AdnanMula\Criteria\Filter\FilterType;
use AdnanMula\Criteria\FilterField\FilterField;
use AdnanMula\Criteria\FilterValue\ArrayElementFilterValue;
use AdnanMula\Criteria\FilterValue\StringFilterValue;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Contracts\HttpClient\HttpClientInterface;

final readonly class ImportDeckAllianceFromDokService implements ImportDeckAllianceService
{
    public function __construct(
        private KeyforgeDeckRepository $repository,
        private KeyforgeDeckAllianceRepository $allianceRepository,
        private KeyforgeCardRepository $cardRepository,
        private HttpClientInterface $dokClient,
        private DeckApplyPredefinedTagsService $tagsService,
    ) {}

    public function execute(Uuid $uuid, ?Uuid $owner = null, bool $forceUpdate = false): KeyforgeDeck
    {
        $deck = $this->repository->searchOne(new Criteria(
            new Filters(
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

        [$scalingAmberCards, $boardClearCards] = $this->specialCards();

        $newDeck = new KeyforgeDeck(
            Uuid::from($deckResponse['deck']['keyforgeId']),
            $deckResponse['deck']['id'],
            KeyforgeDeckType::ALLIANCE,
            $deckResponse['deck']['name'],
            KeyforgeSet::fromDokName($deckResponse['deck']['expansion']),
            KeyforgeDeckHouses::fromDokData($deckResponse),
            KeyforgeCards::fromDokData($deckResponse),
            KeyforgeDeckStats::fromDokData($deckResponse, $scalingAmberCards, $boardClearCards),
        );

        $this->repository->save($newDeck);

        $this->allianceRepository->saveComposition($newDeck->id(), [
            'pods' => $deckResponse['deck']['allianceHouses'],
            'extraCards' => $newDeck->cards()->extraCards,
        ]);

        if (null !== $owner) {
            $this->repository->addOwner($newDeck->id(), $owner);
        }

        $this->tagsService->execute($newDeck->id());

        return $newDeck;
    }

    private function specialCards(): array
    {
        $scalingAmberCards = $this->cardRepository->search(
            new Criteria(
                new Filters(
                    FilterType::AND,
                    new Filter(
                        new FilterField('tags'),
                        new ArrayElementFilterValue('scalingAmberControl'),
                        FilterOperator::IN_ARRAY,
                    ),
                ),
            ),
        );

        $boardClearsCards = $this->cardRepository->search(
            new Criteria(
                new Filters(
                    FilterType::AND,
                    new Filter(
                        new FilterField('tags'),
                        new ArrayElementFilterValue('boardClear'),
                        FilterOperator::IN_ARRAY,
                    ),
                ),
            ),
        );

        return [
            \array_map(static fn (KeyforgeCard $c): string => $c->nameUrl, $scalingAmberCards),
            \array_map(static fn (KeyforgeCard $c): string => $c->nameUrl, $boardClearsCards),
        ];
    }
}
