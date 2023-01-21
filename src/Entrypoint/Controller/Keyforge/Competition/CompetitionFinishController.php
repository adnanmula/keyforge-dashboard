<?php declare(strict_types=1);

namespace AdnanMula\Cards\Entrypoint\Controller\Keyforge\Competition;

use AdnanMula\Cards\Application\Command\Keyforge\Competition\Finish\FinishCompetitionCommand;
use AdnanMula\Cards\Entrypoint\Controller\Shared\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

final class CompetitionFinishController extends Controller
{
    public function __invoke(Request $request): Response
    {
        $this->assertIsLogged();

        if (false === $this->security->isGranted('ROLE_KEYFORGE')) {
            throw new AccessDeniedException();
        }

        $this->bus->dispatch(new FinishCompetitionCommand(
            $request->get('competitionId'),
            $request->get('winnerId'),
            $request->get('date'),
        ));

        return new Response('', Response::HTTP_OK);
    }
}
