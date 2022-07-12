<?php declare(strict_types=1);

namespace AdnanMula\Cards\Entrypoint\Controller\Keyforge;

use AdnanMula\Cards\Application\Keyforge\AddGame\AddKeyforgeGameCommand;
use AdnanMula\Cards\Application\Keyforge\Get\GetKeyforgeDecksQuery;
use AdnanMula\Cards\Application\Keyforge\Get\GetKeyforgeUsersQuery;
use AdnanMula\Cards\Domain\Model\Keyforge\KeyforgeDeck;
use AdnanMula\Cards\Domain\Model\User\User;
use AdnanMula\Cards\Entrypoint\Controller\Shared\QueryController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

final class AddGameController extends QueryController
{
    public function __invoke(Request $request): Response
    {
        $users = $this->extractResult(
            $this->bus->dispatch(new GetKeyforgeUsersQuery(0, 1000))
        );

        $decks = $this->extractResult(
            $this->bus->dispatch(new GetKeyforgeDecksQuery(0, 1000))
        );

        $users = \array_map(static fn (User $user) => ['id' => $user->id()->value(), 'name' => $user->name()], $users);
        $decks = \array_map(static fn (KeyforgeDeck $deck) => ['id' => $deck->id()->value(), 'name' => $deck->name()], $decks);

        if ($request->getMethod() === Request::METHOD_GET) {
            return $this->render(
                'Keyforge/add_game.html.twig',
                [
                    'winners' =>$users,
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
                $this->bus->dispatch(new AddKeyforgeGameCommand(
                    $request->request->get('winner'),
                    $request->request->get('winnerDeck'),
                    $request->request->get('loser'),
                    $request->request->get('loserDeck'),
                    $request->request->get('loserScore'),
                    $request->request->get('firstTurn'),
                    '2000-11-11 10:00:00',
                ));

                return $this->render(
                    'Keyforge/add_game.html.twig',
                    [
                        'winners' =>$users,
                        'winnerDecks' => $decks,
                        'losers' => $users,
                        'loserDecks' => $decks,
                        'result' => 'Que bien jugado caralmendra',
                        'success' => true
                    ],
                );
            } catch (\Throwable $exception) {
                return $this->render(
                    'Keyforge/add_game.html.twig',
                    [
                        'winners' =>$users,
                        'winnerDecks' => $decks,
                        'losers' => $users,
                        'loserDecks' => $decks,
                        'result' => $exception->getMessage(),
                        'success' => false
                    ],
                );
            }
        }

        throw new \InvalidArgumentException('Error');
    }
}
