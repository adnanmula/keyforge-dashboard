<?php declare(strict_types=1);

namespace AdnanMula\Cards\Application\Query\Keyforge\Game;

use AdnanMula\Cards\Domain\Model\Keyforge\Deck\KeyforgeDeckRepository;
use AdnanMula\Cards\Domain\Model\Keyforge\Game\KeyforgeGameRepository;
use AdnanMula\Cards\Domain\Model\Keyforge\User\KeyforgeUserRepository;
use AdnanMula\Cards\Domain\Model\Shared\User;
use AdnanMula\Cards\Domain\Model\Shared\ValueObject\Uuid;
use AdnanMula\Criteria\Criteria;
use AdnanMula\Criteria\Filter\Filter;
use AdnanMula\Criteria\Filter\FilterOperator;
use AdnanMula\Criteria\Filter\Filters;
use AdnanMula\Criteria\Filter\FilterType;
use AdnanMula\Criteria\FilterField\FilterField;
use AdnanMula\Criteria\FilterValue\StringArrayFilterValue;
use Symfony\Bundle\SecurityBundle\Security;

final readonly class GetGamesQueryHandler
{
    public function __construct(
        private KeyforgeGameRepository $gameRepository,
        private KeyforgeDeckRepository $deckRepository,
        private KeyforgeUserRepository $userRepository,
        private Security $security,
    ) {}

    public function __invoke(GetGamesQuery $query): array
    {
        /** @var ?User $currentUser */
        $currentUser = $this->security->getUser();

        $games = $this->gameRepository->search($query->criteria);
        $total = $this->gameRepository->count($query->criteria->withoutPaginationAndSorting());

        $userIds = [];
        $decksIds = [];

        foreach ($games as $game) {
            $userIds[] = $game->winner();
            $userIds[] = $game->loser();
            $userIds[] = $game->firstTurn();
            $decksIds[] = $game->winnerDeck();
            $decksIds[] = $game->loserDeck();
        }

        $userIds = \array_values(\array_unique(\array_filter($userIds)));

        $decks = $this->deckRepository->search(new Criteria(
            new Filters(
                FilterType::AND,
                new Filter(new FilterField('id'), new StringArrayFilterValue(...\array_map(static fn (Uuid $id): string => $id->value(), $decksIds)), FilterOperator::IN),
            ),
        ));

        $users = $this->userRepository->search(new Criteria(
            new Filters(
                FilterType::AND,
                new Filter(new FilterField('id'), new StringArrayFilterValue(...\array_map(static fn (Uuid $id): string => $id->value(), $userIds)), FilterOperator::IN),
            ),
        ));

        $indexedDecks = [];

        foreach ($decks as $deck) {
            $indexedDecks[$deck->id()->value()] = $deck->name();
        }

        $indexedUsers = [];

        if (null !== $currentUser) {
            foreach ($users as $user) {
                if (null === $user->owner() || $user->owner()->equalTo($currentUser->id())) {
                    $indexedUsers[$user->id()->value()] = $user->name();
                }
            }
        }

        $result = [];

        foreach ($games as $game) {
            $result[] = [
                'id' => $game->id()->value(),
                'winner' => array_key_exists($game->winner()->value(), $indexedUsers)
                    ? $game->winner()->value()
                    : null,
                'winner_name' => $indexedUsers[$game->winner()->value()] ?? 'Anonymous',
                'winner_deck' => $game->winnerDeck()->value(),
                'winner_deck_name' => $indexedDecks[$game->winnerDeck()->value()],
                'loser' => array_key_exists($game->loser()->value(), $indexedUsers)
                    ? $game->loser()->value()
                    : null,
                'loser_name' => $indexedUsers[$game->loser()->value()] ?? 'Anonymous',
                'loser_deck' => $game->loserDeck()->value(),
                'loser_deck_name' => $indexedDecks[$game->loserDeck()->value()],
                'score' => $game->score()->winnerScore() . '/' . $game->score()->loserScore(),
                'first_turn' => null === $game->firstTurn()
                    ? null
                    : $indexedUsers[$game->firstTurn()->value()] ?? null,
                'date' => $game->date()->format('Y-m-d'),
                'competition' => $game->competition()->value,
                'notes' => null !== $currentUser
                    ? $game->notes()
                    : '',
                'logId' => $game->logId()?->value(),
            ];
        }

        return [
            'games' => $result,
            'total' => $total,
            'totalFiltered' => $total,
        ];
    }
}
