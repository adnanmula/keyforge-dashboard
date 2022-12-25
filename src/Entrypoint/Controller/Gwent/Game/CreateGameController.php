<?php declare(strict_types=1);

namespace AdnanMula\Cards\Entrypoint\Controller\Gwent\Game;

use AdnanMula\Cards\Application\Command\Gwent\Game\Create\CreateGameCommand;
use AdnanMula\Cards\Application\Query\Keyforge\Deck\GetDecksQuery;
use AdnanMula\Cards\Domain\Model\Gwent\GwentDeck;
use AdnanMula\Cards\Domain\Model\Gwent\GwentUser;
use AdnanMula\Cards\Entrypoint\Controller\Shared\Controller;
use AdnanMula\Cards\Infrastructure\Criteria\FilterField\FilterField;
use AdnanMula\Cards\Infrastructure\Criteria\Sorting\Order;
use AdnanMula\Cards\Infrastructure\Criteria\Sorting\OrderType;
use AdnanMula\Cards\Infrastructure\Criteria\Sorting\Sorting;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

final class CreateGameController extends Controller
{
    public function __invoke(Request $request): Response
    {
        $users = [];

        $decks = $this->extractResult(
            $this->bus->dispatch(new GetDecksQuery(null, null, null, null, null, null, new Sorting(new Order(new FilterField('name'), OrderType::ASC)))),
        );

        $users = \array_map(static fn (GwentUser $user) => ['id' => $user->id()->value(), 'name' => $user->name()], $users);
        $decks = \array_map(static fn (GwentDeck $deck) => ['id' => $deck->id()->value(), 'name' => $deck->name()], $decks['decks']);

        if ($request->getMethod() === Request::METHOD_GET) {
            return $this->render(
                'Keyforge/Game/create_game.html.twig',
                [
                    'winners' => $users,
                    'winnerDecks' => $decks,
                    'losers' => $users,
                    'loserDecks' => $decks,
                    'result' => false,
                    'success' => null,
                ],
            );
        }

        if ($request->getMethod() === Request::METHOD_POST) {
            try {
                $this->bus->dispatch(new CreateGameCommand(
                    $request->request->get('userId'),
                    $request->request->get('userDeckId'),
                    $request->request->get('opponentArchetypeId'),
                    $request->request->get('win'),
                    $request->request->get('coin'),
                    $request->request->get('rank'),
                    $request->request->get('userScore'),
                    $request->request->get('opponentScore'),
                    $request->request->get('date'),
                ));

                return $this->render(
                    'Gwent/Game/create_game.html.twig',
                    [
                        'winners' =>$users,
                        'winnerDecks' => $decks,
                        'losers' => $users,
                        'loserDecks' => $decks,
                        'result' => 'Que bien jugado caralmendra',
                        'success' => true,
                    ],
                );
            } catch (\Throwable $exception) {
                return $this->render(
                    'Gwent/Game/create_game.html.twig',
                    [
                        'winners' =>$users,
                        'winnerDecks' => $decks,
                        'losers' => $users,
                        'loserDecks' => $decks,
                        'result' => $exception->getMessage(),
                        'success' => true,
                    ],
                );
            }
        }

        throw new \InvalidArgumentException('Error');
    }
}
