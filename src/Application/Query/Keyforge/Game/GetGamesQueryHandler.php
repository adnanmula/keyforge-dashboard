<?php declare(strict_types=1);

namespace AdnanMula\Cards\Application\Query\Keyforge\Game;

use AdnanMula\Cards\Domain\Model\Keyforge\Deck\KeyforgeDeckRepository;
use AdnanMula\Cards\Domain\Model\Keyforge\Game\KeyforgeGameRepository;
use AdnanMula\Cards\Domain\Model\Keyforge\User\KeyforgeUserRepository;
use AdnanMula\Cards\Domain\Model\Shared\ValueObject\Uuid;
use AdnanMula\Criteria\Criteria;
use AdnanMula\Criteria\Filter\Filter;
use AdnanMula\Criteria\Filter\FilterType;
use AdnanMula\Criteria\FilterField\FilterField;
use AdnanMula\Criteria\FilterGroup\AndFilterGroup;
use AdnanMula\Criteria\FilterValue\FilterOperator;
use AdnanMula\Criteria\FilterValue\StringArrayFilterValue;

final class GetGamesQueryHandler
{
    public function __construct(
        private KeyforgeGameRepository $gameRepository,
        private KeyforgeDeckRepository $deckRepository,
        private KeyforgeUserRepository $userRepository,
    ) {}

    public function __invoke(GetGamesQuery $query): array
    {
        $games = $this->gameRepository->search($query->criteria());

        $criteriaWithoutOrder = new Criteria(
            null,
            null,
            null,
            ...$query->criteria()->filterGroups(),
        );

        $total = $this->gameRepository->count($criteriaWithoutOrder);

        $userIds = [];
        $decksIds = [];

        foreach ($games as $game) {
            $userIds[] = $game->winner();
            $userIds[] = $game->loser();
            $userIds[] = $game->firstTurn();
            $decksIds[] = $game->winnerDeck();
            $decksIds[] = $game->loserDeck();
        }

        $decks = $this->deckRepository->search(new Criteria(
            null,
            null,
            null,
            new AndFilterGroup(
                FilterType::AND,
                new Filter(new FilterField('id'), new StringArrayFilterValue(...\array_map(static fn (Uuid $id): string => $id->value(), $decksIds)), FilterOperator::IN),
            ),
        ));

        $users = $this->userRepository->search(new Criteria(
            null,
            null,
            null,
            new AndFilterGroup(
                FilterType::AND,
                new Filter(new FilterField('id'), new StringArrayFilterValue(...\array_map(static fn (Uuid $id): string => $id->value(), $userIds)), FilterOperator::IN),
            ),
        ));

        $indexedDecks = [];

        foreach ($decks as $deck) {
            $indexedDecks[$deck->id()->value()] = $deck->name();
        }

        $indexedUsers = [];

        foreach ($users as $user) {
            $indexedUsers[$user->id()->value()] = $user->name();
        }

        $result = [];

        foreach ($games as $game) {
            $result[] = [
                'winner' => $game->winner()->value(),
                'winner_name' => $indexedUsers[$game->winner()->value()],
                'winner_deck' => $game->winnerDeck()->value(),
                'winner_deck_name' => $indexedDecks[$game->winnerDeck()->value()],
                'loser' => $game->loser()->value(),
                'loser_name' => $indexedUsers[$game->loser()->value()],
                'loser_deck' => $game->loserDeck()->value(),
                'loser_deck_name' => $indexedDecks[$game->loserDeck()->value()],
                'score' => $game->score()->winnerScore() . '/' . $game->score()->loserScore(),
                'first_turn' => null === $game->firstTurn() ? null : $indexedUsers[$game->firstTurn()->value()],
                'date' => $game->date()->format('Y-m-d'),
                'competition' => $game->competition()->value,
                'notes' => $game->notes(),
            ];
        }

        return [
            'games' => $result,
            'total' => $total,
            'totalFiltered' => $total,
        ];
    }
}
