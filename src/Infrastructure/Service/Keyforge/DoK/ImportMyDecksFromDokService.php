<?php declare(strict_types=1);

namespace AdnanMula\Cards\Infrastructure\Service\Keyforge\DoK;

use AdnanMula\Cards\Domain\Model\Keyforge\Card\KeyforgeCard;
use AdnanMula\Cards\Domain\Model\Keyforge\Card\KeyforgeCardRepository;
use AdnanMula\Cards\Domain\Model\Keyforge\Deck\KeyforgeDeck;
use AdnanMula\Cards\Domain\Model\Keyforge\Deck\KeyforgeDeckRepository;
use AdnanMula\Cards\Domain\Model\Keyforge\Deck\ValueObject\KeyforgeCards;
use AdnanMula\Cards\Domain\Model\Keyforge\Deck\ValueObject\KeyforgeDeckHouses;
use AdnanMula\Cards\Domain\Model\Keyforge\Deck\ValueObject\KeyforgeDeckStats;
use AdnanMula\Cards\Domain\Model\Keyforge\Deck\ValueObject\KeyforgeDeckType;
use AdnanMula\Cards\Domain\Model\Keyforge\Deck\ValueObject\KeyforgeSet;
use AdnanMula\Cards\Domain\Model\Shared\ValueObject\Uuid;
use AdnanMula\Cards\Domain\Service\Keyforge\Deck\DeckApplyPredefinedTagsService;
use AdnanMula\Criteria\Criteria;
use AdnanMula\Criteria\Filter\Filter;
use AdnanMula\Criteria\Filter\FilterOperator;
use AdnanMula\Criteria\Filter\Filters;
use AdnanMula\Criteria\Filter\FilterType;
use AdnanMula\Criteria\FilterField\FilterField;
use AdnanMula\Criteria\FilterValue\ArrayElementFilterValue;
use AdnanMula\Criteria\FilterValue\IntFilterValue;
use AdnanMula\Criteria\FilterValue\StringArrayFilterValue;
use Symfony\Component\DependencyInjection\Attribute\Target;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Contracts\HttpClient\HttpClientInterface;

final readonly class ImportMyDecksFromDokService
{
    public function __construct(
        #[Target('dokClient')]
        private HttpClientInterface $dokClient,
        private KeyforgeDeckRepository $repository,
        private KeyforgeCardRepository $cardRepository,
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

        $deckIds = \array_map(static fn (array $deck): string => $deck['deck']['keyforgeId'], $response);

        $storedDecks = $this->repository->search(
            new Criteria(
                new Filters(
                    FilterType::AND,
                    new Filter(new FilterField('id'), new StringArrayFilterValue(...$deckIds), FilterOperator::IN),
                ),
            ),
        );

        [$scalingAmberCards, $boardClearCards, $giganticCards] = $this->specialCards();
        $newDecks = [];

        foreach ($response as $responseDeck) {
            /** @var ?KeyforgeDeck $storedDeck */
            $storedDeck = \array_values(\array_filter(
                $storedDecks,
                static fn (KeyforgeDeck $d) => $d->id()->value() === $responseDeck['deck']['keyforgeId'],
            ))[0] ?? null;

            if (false === $forceUpdate && null !== $storedDeck) {
                continue;
            }

            $newDecks[] = new KeyforgeDeck(
                Uuid::from($responseDeck['deck']['keyforgeId']),
                $responseDeck['deck']['id'],
                KeyforgeDeckType::STANDARD,
                $responseDeck['deck']['name'],
                KeyforgeSet::fromDokName($responseDeck['deck']['expansion']),
                KeyforgeDeckHouses::fromDokData($responseDeck),
                KeyforgeCards::fromDokData($responseDeck),
                KeyforgeDeckStats::fromDokData($responseDeck, $scalingAmberCards, $boardClearCards),
            );
        }

        $newDecks = $this->tagsService->execute($newDecks, $scalingAmberCards, $boardClearCards, $giganticCards, false);

        $this->repository->save(...$newDecks);
        $this->repository->addOwner(
            $owner,
            ...\array_map(static fn (KeyforgeDeck $d): Uuid => $d->id(), $newDecks),
        );
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

        $giganticCards = $this->cardRepository->search(
            new Criteria(
                new Filters(
                    FilterType::AND,
                    new Filter(
                        new FilterField('is_big'),
                        new IntFilterValue(1),
                        FilterOperator::EQUAL,
                    ),
                ),
            ),
        );

        return [
            \array_map(static fn (KeyforgeCard $c): string => $c->nameUrl, $scalingAmberCards),
            \array_map(static fn (KeyforgeCard $c): string => $c->nameUrl, $boardClearsCards),
            \array_map(static fn (KeyforgeCard $c): string => $c->nameUrl, $giganticCards),
        ];
    }
}
