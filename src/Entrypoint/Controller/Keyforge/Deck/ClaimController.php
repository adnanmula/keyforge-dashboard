<?php declare(strict_types=1);

namespace AdnanMula\Cards\Entrypoint\Controller\Keyforge\Deck;

use AdnanMula\Cards\Application\Command\Keyforge\Deck\Claim\ClaimDeckCommand;
use AdnanMula\Cards\Domain\Model\Shared\User;
use AdnanMula\Cards\Entrypoint\Controller\Shared\Controller;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Messenger\MessageBusInterface;

final class ClaimController extends Controller
{
    private Security $security;

    public function __construct(MessageBusInterface $bus, Security $security)
    {
        $this->security = $security;

        parent::__construct($bus);
    }

    public function __invoke(Request $request): Response
    {
        if (false === $this->security->isGranted('ROLE_KEYFORGE')) {
            return new Response('Forbidden', Response::HTTP_FORBIDDEN);
        }

        /** @var User $user */
        $user = $this->security->getUser();

        $this->bus->dispatch(new ClaimDeckCommand(
            $user->id()->value(),
            $request->get('deckId'),
        ));

        return new Response('', Response::HTTP_OK);
    }
}
