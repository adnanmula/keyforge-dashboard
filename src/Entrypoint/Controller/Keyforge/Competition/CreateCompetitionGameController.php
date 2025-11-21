<?php declare(strict_types=1);

namespace AdnanMula\Cards\Entrypoint\Controller\Keyforge\Competition;

use AdnanMula\Cards\Application\Command\Keyforge\Game\Create\CreateCompetitionGameCommand;
use AdnanMula\Cards\Entrypoint\Controller\Shared\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

final class CreateCompetitionGameController extends Controller
{
    public function __invoke(Request $request, string $fixtureId): Response
    {
        $this->assertIsLogged();

        $this->validateCsrfToken('keyforge_competition_game_create', $request->get('_csrf_token'));

        $this->bus->dispatch(new CreateCompetitionGameCommand(
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
            $request->request->get('log'),
        ));

        return new JsonResponse();
    }
}
