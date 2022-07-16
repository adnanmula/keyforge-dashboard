<?php declare(strict_types=1);

namespace AdnanMula\Cards\Entrypoint\Controller\Keyforge\Stats\Deck;

use AdnanMula\Cards\Application\Query\Keyforge\Deck\GetDecksQuery;
use AdnanMula\Cards\Entrypoint\Controller\Shared\Controller;
use Symfony\Component\HttpFoundation\Response;

final class ListDecksController extends Controller
{
    public function __invoke(): Response
    {
        $decks = $this->extractResult(
            $this->bus->dispatch(new GetDecksQuery(0, 25)),
        );

        return $this->render('Keyforge/Stats/Deck/list_decks.html.twig', ['decks' => $decks]);
    }
}
