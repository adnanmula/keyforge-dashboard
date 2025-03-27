<?php declare(strict_types=1);

namespace AdnanMula\Cards\Entrypoint\Controller\Keyforge\Deck\Detail;

use AdnanMula\Cards\Application\Query\Keyforge\Deck\GetDecksQuery;
use AdnanMula\Cards\Application\Query\Keyforge\Deck\GetDecksStatHistoryQuery;
use AdnanMula\Cards\Application\Query\Keyforge\Game\GetGamesQuery;
use AdnanMula\Cards\Application\Query\Keyforge\User\GetUsersQuery;
use AdnanMula\Cards\Domain\Model\Keyforge\Card\KeyforgeCardRepository;
use AdnanMula\Cards\Domain\Model\Keyforge\Card\ValueObject\KeyforgeCardType;
use AdnanMula\Cards\Domain\Model\Keyforge\Deck\Exception\DeckNotExistsException;
use AdnanMula\Cards\Domain\Model\Keyforge\Deck\KeyforgeDeck;
use AdnanMula\Cards\Domain\Model\Keyforge\Deck\KeyforgeDeckRepository;
use AdnanMula\Cards\Domain\Model\Shared\User;
use AdnanMula\Cards\Domain\Model\Shared\UserRepository;
use AdnanMula\Cards\Domain\Model\Shared\ValueObject\Uuid;
use AdnanMula\Cards\Entrypoint\Controller\Shared\Controller;
use AdnanMula\Criteria\Criteria;
use AdnanMula\Criteria\Filter\Filter;
use AdnanMula\Criteria\Filter\FilterType;
use AdnanMula\Criteria\FilterField\FilterField;
use AdnanMula\Criteria\FilterGroup\AndFilterGroup;
use AdnanMula\Criteria\FilterValue\FilterOperator;
use AdnanMula\Criteria\FilterValue\StringArrayFilterValue;
use AdnanMula\Criteria\FilterValue\StringFilterValue;
use AdnanMula\Criteria\Sorting\OrderType;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Translation\LocaleSwitcher;
use Symfony\Contracts\Translation\TranslatorInterface;

final class DeckDetailController extends Controller
{
    public function __construct(
        MessageBusInterface $bus,
        Security $security,
        LocaleSwitcher $localeSwitcher,
        TranslatorInterface $translator,
        private KeyforgeDeckRepository $deckRepository,
        private KeyforgeCardRepository $cardRepository,
        private UserRepository $userRepository,
    ) {
        parent::__construct($bus, $security, $localeSwitcher, $translator);
    }

    public function __invoke(Request $request, string $deckId): Response
    {
        /** @var ?User $user */
        $user = $this->security->getUser();
        $deck = $this->deck($user?->id(), $deckId);

        $indexedFriends = [];

        if (null !== $user) {
            $friends = $this->userRepository->friends($user->id(), false);

            foreach ($friends as $friend) {
                $indexedFriends[$friend['id']] = $friend['sender_name'];
                $indexedFriends[$friend['friend_id']] = $friend['receiver_name'];
            }
        }

        return $this->render(
            'Keyforge/Deck/Detail/deck_detail.html.twig',
            [
                'reference' => $deck->id()->value(),
                'userId' => $user?->id()?->value(),
                'deck_name' => $deck->name(),
                'deck_owner' => $deck->userData()?->userId()?->value(),
                'deck_owners' => \array_map(static fn (Uuid $id): string => $id->value(), $deck->owners()),
                'deck_owner_name' => $this->ownerName($deck),
                'deck' => $deck->jsonSerialize(),
                'deck_notes' => $this->notes($user, $deck),
                'deck_history' => $this->deckHistory($deckId),
                'stats' => $this->stats($deck),
                'indexed_friends' => $indexedFriends,
                'deck_card_types' => $this->cardTypes($deck),
                'bell_curve' => $this->deckRepository->bellCurve($deck->type()),
            ],
        );
    }

    private function deck(?Uuid $userId, string $deckId): KeyforgeDeck
    {
        $deck = $this->extractResult(
            $this->bus->dispatch(new GetDecksQuery(0, 1, null, 'id', OrderType::ASC->value, null, null, null, null, $deckId, $userId?->value())),
        );

        if (\count($deck['decks']) === 0) {
            throw new DeckNotExistsException();
        }

        $deck = $this->deckRepository->searchOne(
            new Criteria(
                null,
                null,
                null,
                new AndFilterGroup(
                    FilterType::AND,
                    new Filter(new FilterField('id'), new StringFilterValue($deckId), FilterOperator::EQUAL),
                ),
            ),
        );

        if (null === $deck) {
            throw new DeckNotExistsException();
        }

        return $deck;
    }

    private function deckHistory(string $deckId): array
    {
        $stats = $this->extractResult(
            $this->bus->dispatch(new GetDecksStatHistoryQuery($deckId)),
        );

        return $stats[$deckId] ?? [];
    }

    private function ownerName(KeyforgeDeck $deck): ?string
    {
        if (null !== $deck->userData()?->userId()) {
            $users = $this->extractResult(
                $this->bus->dispatch(new GetUsersQuery(0, 1000, false, false)),
            );

            foreach ($users as $user) {
                if ($user->id()->value() === $deck->userData()->userId()) {
                    return $user->name();
                }
            }
        }

        return null;
    }

    private function notes(?User $user, KeyforgeDeck $deck): ?string
    {
        if (null === $user) {
            return null;
        }

        $decksOwnership = $this->deckRepository->ownersOf($deck->id());

        foreach ($decksOwnership as $deckOwnership) {
            if ($deckOwnership['user_id'] === $user->id()->value()) {
                return $deckOwnership['notes'];
            }
        }

        return null;
    }

    private function stats(KeyforgeDeck $deck): array
    {
        $criteria = new Criteria(
            null,
            null,
            null,
            new AndFilterGroup(
                FilterType::OR,
                new Filter(new FilterField('winner_deck'), new StringFilterValue($deck->id()->value()), FilterOperator::EQUAL),
                new Filter(new FilterField('loser_deck'), new StringFilterValue($deck->id()->value()), FilterOperator::EQUAL),
            ),
        );

        $games = $this->extractResult($this->bus->dispatch(new GetGamesQuery(criteria: $criteria)));

        $winRateVsDeck = [];

        foreach ($games['games'] as $game) {
            $isWin = $game['winner_deck'] === $deck->id()->value();

            if ($isWin) {
                if (\array_key_exists($game['loser_deck'], $winRateVsDeck)) {
                    $winRateVsDeck[$game['loser_deck']] = [
                        'wins' => $winRateVsDeck[$game['loser_deck']]['wins'] + 1,
                        'losses' => $winRateVsDeck[$game['loser_deck']]['losses'],
                    ];
                } else {
                    $winRateVsDeck[$game['loser_deck']] = [
                        'wins' => 1,
                        'losses' => 0,
                    ];
                }
            } else {
                if (\array_key_exists($game['winner_deck'], $winRateVsDeck)) {
                    $winRateVsDeck[$game['winner_deck']] = [
                        'wins' => $winRateVsDeck[$game['winner_deck']]['wins'],
                        'losses' => $winRateVsDeck[$game['winner_deck']]['losses'] + 1,
                    ];
                } else {
                    $winRateVsDeck[$game['winner_deck']] = [
                        'wins' => 0,
                        'losses' => 1,
                    ];
                }
            }
        }

        $currentRival = null;
        $currentNemesis = null;

        foreach ($winRateVsDeck as $id => $stats) {
            if (null === $currentRival || $currentRival['games'] < $stats['wins'] + $stats['losses']) {
                $currentRival = ['id' => $id, 'games' => $stats['wins'] + $stats['losses']];
            }

            if (null === $currentNemesis || $currentNemesis['losses'] < $stats['losses']) {
                if ($stats['losses'] > 0) {
                    $currentNemesis = ['id' => $id, 'losses' => $stats['losses']];
                }
            }
        }

        if (null !== $currentNemesis) {
            $currentNemesis['name'] = $this->deck(null, $currentNemesis['id'])->name();
        }

        if (null !== $currentRival) {
            $currentRival['name'] = $this->deck(null, $currentRival['id'])->name();
        }

        return [
            'wins' => $deck->userData()?->winsVsUsers(),
            'losses' => $deck->userData()?->lossesVsUsers(),
            'rival' => $currentRival,
            'nemesis' => $currentNemesis,
        ];
    }

    public function cardTypes(KeyforgeDeck $deck): array
    {
        $cardNames = [];

        foreach (array_merge($deck->cards()->firstPodCards, $deck->cards()->secondPodCards, $deck->cards()->thirdPodCards) as $card) {
            $cardNames[] = $card->serializedName;
        }

        $cards = $this->cardRepository->search(
            new Criteria(
                null,
                null,
                null,
                new AndFilterGroup(
                    FilterType::AND,
                    new Filter(new FilterField('name_url'), new StringArrayFilterValue(...$cardNames), FilterOperator::IN),
                ),
            ),
        );

        $indexedCards = [];
        foreach ($cards as $card) {
            $indexedCards[$card->nameUrl] = $card;
        }

        $cardTypes = [
            KeyforgeCardType::ACTION->value => 0,
            KeyforgeCardType::CREATURE->value => 0,
            KeyforgeCardType::ARTIFACT->value => 0,
            KeyforgeCardType::UPGRADE->value => 0,
        ];

        foreach ($cardNames as $cardName) {
            $cardTypes[$indexedCards[$cardName]->type->value]++;
        }

        return $cardTypes;
    }
}
