<?php declare(strict_types=1);

namespace AdnanMula\Cards\Entrypoint\Controller\Keyforge\Competition;

use AdnanMula\Cards\Application\Command\Keyforge\Competition\Start\StartCompetitionCommand;
use AdnanMula\Cards\Entrypoint\Controller\Shared\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

final class CompetitionStartController extends Controller
{
    public function __invoke(Request $request): Response
    {
        $this->assertIsLogged();

        if (false === $this->security->isGranted('ROLE_KEYFORGE')) {
            throw new AccessDeniedException();
        }

        $this->bus->dispatch(new StartCompetitionCommand(
            $request->get('competitionId'),
            $request->get('date', (new \DateTimeImmutable())->format('Y-m-d')),
        ));

        return new Response('', Response::HTTP_OK);
    }
}
