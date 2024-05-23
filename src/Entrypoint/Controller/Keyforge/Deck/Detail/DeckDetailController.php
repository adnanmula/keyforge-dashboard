<?php declare(strict_types=1);

namespace AdnanMula\Cards\Entrypoint\Controller\Keyforge\Deck\Detail;

use AdnanMula\Cards\Application\Command\Keyforge\Deck\Analyze\AnalyzeDeckThreatsCommand;
use AdnanMula\Cards\Application\Query\Keyforge\Deck\GetDecksQuery;
use AdnanMula\Cards\Application\Query\Keyforge\Deck\GetDecksStatHistoryQuery;
use AdnanMula\Cards\Application\Query\Keyforge\Game\GetGamesQuery;
use AdnanMula\Cards\Application\Query\Keyforge\User\GetUsersQuery;
use AdnanMula\Cards\Domain\Model\Keyforge\Deck\Exception\DeckNotExistsException;
use AdnanMula\Cards\Domain\Model\Keyforge\KeyforgeDeck;
use AdnanMula\Cards\Domain\Model\Shared\User;
use AdnanMula\Cards\Domain\Model\Shared\ValueObject\Uuid;
use AdnanMula\Cards\Entrypoint\Controller\Shared\Controller;
use AdnanMula\Criteria\Criteria;
use AdnanMula\Criteria\Filter\Filter;
use AdnanMula\Criteria\Filter\FilterType;
use AdnanMula\Criteria\FilterField\FilterField;
use AdnanMula\Criteria\FilterGroup\AndFilterGroup;
use AdnanMula\Criteria\FilterValue\FilterOperator;
use AdnanMula\Criteria\FilterValue\StringFilterValue;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

final class DeckDetailController extends Controller
{
    public function __invoke(Request $request, string $deckId): Response
    {
        /** @var ?User $user */
        $user = $this->security->getUser();
        $deck = $this->deck($user?->id(), $deckId);

        $analysis = $this->extractResult($this->bus->dispatch(new AnalyzeDeckThreatsCommand($deck->id()->value())));

        return $this->render(
            'Keyforge/Deck/Detail/deck_detail.html.twig',
            [
                'reference' => $deck->id()->value(),
                'userId' => $user?->id()?->value(),
                'deck_name' => $deck->data()->name,
                'deck_owner' => $deck->userData()->owner?->value(),
                'deck_owner_name' => $this->ownerName($deck),
                'deck' => $deck->jsonSerialize(),
                'deck_notes' => $this->notes($user, $deck),
                'deck_history' => $this->deckHistory($deckId),
                'analysis' => $analysis['detail'],
                'stats' => $this->stats($deck),
            ],
        );
    }

    private function deck(?Uuid $userId, string $deckId): KeyforgeDeck
    {
        $deck = $this->extractResult(
            $this->bus->dispatch(new GetDecksQuery(0, 1, null, null, null, null, null, $deckId, $userId?->value())),
        );

        if (\count($deck['decks']) === 0) {
            throw new DeckNotExistsException();
        }

        return $deck['decks'][0];
    }

    private function deckHistory(string $deckId): array
    {
        $stats = $this->extractResult(
            $this->bus->dispatch(new GetDecksStatHistoryQuery($deckId)),
        );

        return $stats[$deckId];
    }

    private function ownerName(KeyforgeDeck $deck): ?string
    {
        if (null !== $deck->userData()->owner) {
            $users = $this->extractResult(
                $this->bus->dispatch(new GetUsersQuery(0, 1000, false, false)),
            );

            foreach ($users as $user) {
                if ($user->id()->value() === $deck->userData()->owner) {
                    return $user->name();
                }
            }
        }

        return null;
    }

    private function notes(?User $user, KeyforgeDeck $deck): ?string
    {
        if (null !== $user && $user->id()->value() === $deck->userData()->owner?->value()) {
            return $deck->userData()->notes;
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

        $games = $this->extractResult($this->bus->dispatch(new GetGamesQuery($criteria)));

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
            $currentNemesis['name'] = $this->deck(null, $currentNemesis['id'])->data()->name;
        }

        if (null !== $currentRival) {
            $currentRival['name'] = $this->deck(null, $currentRival['id'])->data()->name;
        }

        return [
            'wins' => $deck->userData()->wins,
            'losses' => $deck->userData()->losses,
            'rival' => $currentRival,
            'nemesis' => $currentNemesis,
        ];
    }
}
