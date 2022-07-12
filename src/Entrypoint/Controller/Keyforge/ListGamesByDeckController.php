<?php declare(strict_types=1);

namespace AdnanMula\Cards\Entrypoint\Controller\Keyforge;

use AdnanMula\Cards\Application\Keyforge\GetGames\GetKeyforgeGamesByDeckQuery;
use AdnanMula\Cards\Entrypoint\Controller\Shared\QueryController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

final class ListGamesByDeckController extends QueryController
{
    public function __invoke(Request $request, string $deckId): Response
    {
        $games = $this->extractResult(
            $this->bus->dispatch(new GetKeyforgeGamesByDeckQuery($deckId)),
        );

        return $this->render('Keyforge/list_games.html.twig', ['games' => $games]);
    }
}
