<?php declare(strict_types=1);

namespace AdnanMula\Cards\Entrypoint\Controller\Keyforge\Stats\Game;

use AdnanMula\Cards\Application\Query\Keyforge\Game\GetGamesByDeckQuery;
use AdnanMula\Cards\Entrypoint\Controller\Shared\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

final class ListGamesByDeckController extends Controller
{
    public function __invoke(Request $request, string $deckId): Response
    {
        $games = $this->extractResult(
            $this->bus->dispatch(new GetGamesByDeckQuery($deckId)),
        );

        return $this->render(
            'Keyforge/Stats/Game/list_games_by_deck.html.twig',
            ['games' => $games, 'reference' => $deckId, 'name' => $this->getReferenceName($games[0] ?? null, $deckId)],
        );
    }

    private function getReferenceName(?array $game, string $deckId): string
    {
        if (null === $game) {
            return '';
        }

        if ($game['winner_deck'] === $deckId) {
            return $game['winner_deck_name'];
        }

        if ($game['loser_deck'] === $deckId) {
            return $game['loser_deck_name'];
        }

        return '';
    }
}
