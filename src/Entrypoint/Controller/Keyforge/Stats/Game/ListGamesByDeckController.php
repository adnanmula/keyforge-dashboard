<?php declare(strict_types=1);

namespace AdnanMula\Cards\Entrypoint\Controller\Keyforge\Stats\Game;

use AdnanMula\Cards\Application\Query\Keyforge\Deck\GetDecksQuery;
use AdnanMula\Cards\Application\Query\Keyforge\User\GetUsersQuery;
use AdnanMula\Cards\Entrypoint\Controller\Shared\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

final class ListGamesByDeckController extends Controller
{
    public function __invoke(Request $request, string $deckId): Response
    {
        $userId = $request->get('userId');

        $deck = $this->extractResult(
            $this->bus->dispatch(new GetDecksQuery(0, 1, null, null, null, null, null, $deckId, $userId)),
        );

        $deckName = null;
        $deckOwner = null;
        $deckOwnerName = null;
        $deckSerialized = null;

        if (\count($deck['decks']) > 0) {
            $deckName = $deck['decks'][0]->name();
            $deckOwner = $deck['decks'][0]->owner()?->value();
            $deckSerialized = $deck['decks'][0]->jsonSerialize();
        }

        if (null !== $deckOwner) {
            $users = $this->extractResult(
                $this->bus->dispatch(new GetUsersQuery(0, 1000, false, false)),
            );

            foreach ($users as $user) {
                if ($user->id()->value() === $deckOwner) {
                    $deckOwnerName = $user->name();
                }
            }
        }

        return $this->render(
            'Keyforge/Stats/Game/list_games_by_deck.html.twig',
            [
                'reference' => $deckId,
                'userId' => $userId,
                'deck_name' => $deckName,
                'deck_owner' => $deckOwner,
                'deck_owner_name' => $deckOwnerName,
                'deck' => $deckSerialized,
            ],
        );
    }
}
