<?php declare(strict_types=1);

namespace AdnanMula\Cards\Entrypoint\Controller\Keyforge\Competition;

use AdnanMula\Cards\Application\Command\Keyforge\Game\Create\CreateGameCommand;
use AdnanMula\Cards\Entrypoint\Controller\Shared\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

final class CreateCompetitionGameController extends Controller
{
    public function __invoke(Request $request, string $fixtureId): Response
    {
        $this->bus->dispatch(new CreateGameCommand(
            $request->request->get('winner'),
            $request->request->get('winnerDeck'),
            $request->request->get('winnerChains'),
            $request->request->get('loser'),
            $request->request->get('loserDeck'),
            $request->request->get('loserChains'),
            $request->request->get('loserScore'),
            $request->request->get('firstTurn') === '' ? null : $request->request->get('firstTurn'),
            $request->request->get('date'),
            $request->request->get('competition'),
            $request->request->get('notes'),
            $fixtureId,
        ));

        return new JsonResponse();
    }
}
