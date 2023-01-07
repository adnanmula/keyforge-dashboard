<?php declare(strict_types=1);

namespace AdnanMula\Cards\Entrypoint\Controller\Keyforge\Deck\UpdateNotes;

use AdnanMula\Cards\Application\Command\Keyforge\Deck\UpdateNotes\UpdateDeckNotesCommand;
use AdnanMula\Cards\Domain\Model\Shared\User;
use AdnanMula\Cards\Entrypoint\Controller\Shared\Controller;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

final class UpdateDeckNotesController extends Controller
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
            throw new AccessDeniedException();
        }

        /** @var User $user */
        $user = $this->security->getUser();

        $this->bus->dispatch(new UpdateDeckNotesCommand(
            $request->get('deckId'),
            $request->get('notes'),
            $user->id()->value(),
        ));

        return new Response('', Response::HTTP_OK);
    }
}
