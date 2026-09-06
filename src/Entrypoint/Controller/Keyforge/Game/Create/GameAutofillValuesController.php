<?php declare(strict_types=1);

namespace AdnanMula\Cards\Entrypoint\Controller\Keyforge\Game\Create;

use AdnanMula\Cards\Domain\Model\Keyforge\Deck\KeyforgeDeckRepository;
use AdnanMula\Cards\Domain\Model\Shared\User;
use AdnanMula\Cards\Entrypoint\Controller\Shared\Controller;
use AdnanMula\Criteria\Criteria;
use AdnanMula\Criteria\Filter\Filter;
use AdnanMula\Criteria\Filter\FilterOperator;
use AdnanMula\Criteria\Filter\Filters;
use AdnanMula\Criteria\Filter\FilterType;
use AdnanMula\Criteria\FilterField\FilterField;
use AdnanMula\Criteria\FilterValue\StringFilterValue;
use AdnanMula\KeyforgeGameLogParser\Game\Game;
use AdnanMula\KeyforgeGameLogParser\Parser\GameLogParser;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Translation\LocaleSwitcher;
use Symfony\Contracts\Translation\TranslatorInterface;

class GameAutofillValuesController extends Controller
{
    public function __construct(
        MessageBusInterface $bus,
        Security $security,
        LocaleSwitcher $localeSwitcher,
        TranslatorInterface $translator,
        LoggerInterface $logger,
        private readonly KeyforgeDeckRepository $deckRepository,
    ) {
        parent::__construct($bus, $security, $localeSwitcher, $translator, $logger);
    }

    public function __invoke(Request $request): JsonResponse
    {
        $this->assertIsLogged();
        /** @var User $user */
        $user = $this->getUser();

        $game = $this->game($request->request->get('log'));

        if (null === $game || false === $this->gameIsValid($user, $game)) {
            return new JsonResponse(['game' => ['is_valid' => false]]);
        }

        [$winnerDeckId, $loserDeckId] = $this->deckIds($game);
        [$winnerId, $winnerName, $loserId, $loserName, $firstTurnPlayer] = $this->players($user, $game);

        $response = [
            'game' => [
                'is_valid' => true,
                'type' => 'TCO_COMPETITIVE',
                'firstTurn' => $firstTurnPlayer,
            ],
            'winner' => [
                'id' => $winnerId,
                'name' => $winnerName,
                'deck' => $game->winner()?->deck,
                'deck_id' => $winnerDeckId,
                'score' => $game->winner()?->score,
            ],
            'loser' => [
                'id' => $loserId,
                'name' => $loserName,
                'deck' => $game->loser()?->deck,
                'deck_id' => $loserDeckId,
                'score' => $game->loser()?->score,
            ],
        ];

        return new JsonResponse($response);
    }

    public function game(string $log): ?Game
    {
        $parser = new GameLogParser();

        try {
            return $parser->execute($log);
        } catch (\Throwable) {
            return null;
        }
    }


    private function gameIsValid(User $user, Game $game): bool
    {
        $winner = $game->winner()?->name;
        $loser = $game->loser()?->name;

        if (null === $winner) {
            return false;
        }

        return $winner === $user->tcoName() || $loser === $user->tcoName();
    }

    private function deckIds(Game $game): array
    {
        $decks = $this->deckRepository->search(new Criteria(
            new Filters(
                FilterType::OR,
                new Filter(new FilterField('name'), new StringFilterValue($game->winner()?->deck), FilterOperator::EQUAL),
                new Filter(new FilterField('name'), new StringFilterValue($game->loser()?->deck), FilterOperator::EQUAL),
            ),
        ));

        $indexedDecks = [];
        foreach ($decks as $deck) {
            $indexedDecks[$deck->name()] = $deck->id()->value();
        }

        return [$indexedDecks[$game->winner()->deck] ?? null, $indexedDecks[$game->loser()->deck] ?? null];
    }

    private function players(User $user, Game $game): array
    {
        $winnerId = null;
        $loserId = null;

        if ($game->winner()?->name === $user->tcoName()) {
            $winnerId = $user->id()->value();
        }

        if ($game->loser()?->name === $user->tcoName()) {
            $loserId = $user->id()->value();
        }

        $firstTurnPlayer = $game->first()?->name;

        if ($firstTurnPlayer === $user->tcoName()) {
            $firstTurnPlayer = $user->id()->value();
        }

        return [$winnerId, $game->winner()->name, $loserId, $game->loser()->name, $firstTurnPlayer];
    }
}
