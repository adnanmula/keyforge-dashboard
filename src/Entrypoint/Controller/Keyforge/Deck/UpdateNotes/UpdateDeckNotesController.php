<?php declare(strict_types=1);

namespace AdnanMula\Cards\Entrypoint\Controller\Keyforge\Deck\UpdateNotes;

use AdnanMula\Cards\Application\Command\Keyforge\Deck\UpdateNotes\UpdateDeckNotesCommand;
use AdnanMula\Cards\Domain\Model\Shared\User;
use AdnanMula\Cards\Entrypoint\Controller\Shared\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

final class UpdateDeckNotesController extends Controller
{
    public function __invoke(Request $request): Response
    {
        $this->assertIsLogged();

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
