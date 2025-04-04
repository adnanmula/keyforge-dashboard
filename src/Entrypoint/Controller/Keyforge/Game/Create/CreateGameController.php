<?php declare(strict_types=1);

namespace AdnanMula\Cards\Entrypoint\Controller\Keyforge\Game\Create;

use AdnanMula\Cards\Application\Command\Keyforge\Game\Create\CreateGameCommand;
use AdnanMula\Cards\Application\Query\Keyforge\Deck\GetDecksQuery;
use AdnanMula\Cards\Application\Query\Keyforge\User\GetUsersQuery;
use AdnanMula\Cards\Domain\Model\Keyforge\Deck\KeyforgeDeck;
use AdnanMula\Cards\Domain\Model\Keyforge\Deck\ValueObject\KeyforgeDeckType;
use AdnanMula\Cards\Domain\Model\Keyforge\User\KeyforgeUser;
use AdnanMula\Cards\Domain\Model\Shared\ValueObject\UserRole;
use AdnanMula\Cards\Entrypoint\Controller\Shared\Controller;
use AdnanMula\Criteria\Sorting\OrderType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

final class CreateGameController extends Controller
{
    public function __invoke(Request $request): Response
    {
        $user = $this->getUserWithRole(UserRole::ROLE_KEYFORGE);

        $users = $this->extractResult(
            $this->bus->dispatch(new GetUsersQuery(null, null, false, false, true, $user->id()->value())),
        );

        $decks = $this->extractResult(
            $this->bus->dispatch(new GetDecksQuery(null, null, null, 'name', OrderType::ASC->value, null, null, null, [KeyforgeDeckType::STANDARD->value])),
        );

        $users = \array_map(static fn (KeyforgeUser $user) => ['id' => $user->id()->value(), 'name' => $user->name()], $users);
        $decks = \array_map(static fn (KeyforgeDeck $deck) => ['id' => $deck->id()->value(), 'name' => $deck->name()], $decks['decks']);

        if ($request->getMethod() === Request::METHOD_GET) {
            return $this->render(
                'Keyforge/Game/Create/create_game.html.twig',
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
                $this->bus->dispatch(new CreateGameCommand(
                    $request->request->get('winnerId'),
                    $request->request->get('winnerDeck'),
                    $request->request->get('winnerChains'),
                    $request->request->get('loserId'),
                    $request->request->get('loserDeck'),
                    $request->request->get('loserChains'),
                    $request->request->get('loserScore'),
                    $request->request->get('firstTurnId') === '' ? null : $request->request->get('firstTurnId'),
                    $request->request->get('date'),
                    $request->request->get('competition'),
                    $request->request->get('notes'),
                ));

                return $this->render(
                    'Keyforge/Game/Create/create_game.html.twig',
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
                    'Keyforge/Game/Create/create_game.html.twig',
                    [
                        'winners' =>$users,
                        'winnerDecks' => $decks,
                        'losers' => $users,
                        'loserDecks' => $decks,
                        'result' => $exception->getMessage(),
                        'success' => false,
                    ],
                );
            }
        }

        throw new \InvalidArgumentException('Error');
    }
}
