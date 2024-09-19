<?php declare(strict_types=1);

namespace AdnanMula\Cards\Infrastructure\Service\Keyforge\DoK;

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
use AdnanMula\Criteria\Filter\FilterType;
use AdnanMula\Criteria\FilterField\FilterField;
use AdnanMula\Criteria\FilterGroup\AndFilterGroup;
use AdnanMula\Criteria\FilterValue\FilterOperator;
use AdnanMula\Criteria\FilterValue\StringArrayFilterValue;
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

        $deckIds = \array_map(static fn (array $deck): string => $deck['deck']['keyforgeId'], $response);

        $storedDecks = $this->repository->search(
            new Criteria(
                null,
                null,
                null,
                new AndFilterGroup(
                    FilterType::AND,
                    new Filter(new FilterField('id'), new StringArrayFilterValue(...$deckIds), FilterOperator::IN),
                ),
            ),
        );

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
                $responseDeck['deck']['id'],
                KeyforgeDeckType::STANDARD,
                $responseDeck['deck']['name'],
                KeyforgeSet::fromDokName($responseDeck['deck']['expansion']),
                KeyforgeDeckHouses::fromDokData($responseDeck),
                KeyforgeCards::fromDokData($responseDeck),
                KeyforgeDeckStats::fromDokData($responseDeck),
            );

            $this->repository->save($newDeck);
            $this->repository->addOwner($newDeck->id(), $owner);

            $this->tagsService->execute($newDeck->id());
        }
    }
}
