<?php declare(strict_types=1);

namespace AdnanMula\Cards\Entrypoint\Controller\Keyforge;

use AdnanMula\Cards\Application\Keyforge\Get\GetKeyforgeDecksQuery;
use AdnanMula\Cards\Entrypoint\Controller\Shared\QueryController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

final class ListDecksController extends QueryController
{
    public function __invoke(Request $request): Response
    {
        $decks = $this->extractResult(
            $this->bus->dispatch(new GetKeyforgeDecksQuery(0, 25)),
        );

        return $this->render('Keyforge/list_decks.html.twig', ['decks' => $decks]);
    }
}
