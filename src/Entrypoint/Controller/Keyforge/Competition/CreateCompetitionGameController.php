<?php declare(strict_types=1);

namespace AdnanMula\Cards\Entrypoint\Controller\Keyforge\Competition;

use AdnanMula\Cards\Application\Command\Keyforge\Game\Create\CreateCompetitionGameCommand;
use AdnanMula\Cards\Entrypoint\Controller\Shared\Controller;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

final class CreateCompetitionGameController extends Controller
{
    private Security $security;

    public function __construct(MessageBusInterface $bus, Security $security)
    {
        $this->security = $security;

        parent::__construct($bus);
    }

    public function __invoke(Request $request, string $fixtureId): Response
    {
        if (false === $this->security->isGranted('ROLE_KEYFORGE')) {
            throw new AccessDeniedException();
        }

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
        ));

        return new JsonResponse();
    }
}
