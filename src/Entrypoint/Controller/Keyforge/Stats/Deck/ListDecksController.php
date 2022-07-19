<?php declare(strict_types=1);

namespace AdnanMula\Cards\Entrypoint\Controller\Keyforge\Stats\Deck;

use AdnanMula\Cards\Entrypoint\Controller\Shared\Controller;
use Symfony\Component\HttpFoundation\Response;

final class ListDecksController extends Controller
{
    public function __invoke(): Response
    {
        return $this->render('Keyforge/Stats/Deck/list_decks.html.twig', ['decks' => []]);
    }
}
