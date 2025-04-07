<?php declare(strict_types=1);

namespace AdnanMula\Cards\Entrypoint\Controller\Keyforge\Tag;

use AdnanMula\Cards\Application\Command\Keyforge\Tag\Assign\AssignTagToDeckCommand;
use AdnanMula\Cards\Entrypoint\Controller\Shared\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

final class AssignTagController extends Controller
{
    public function __invoke(Request $request): Response
    {
        $this->assertIsLogged();

        $this->bus->dispatch(new AssignTagToDeckCommand(
            $request->get('deckId'),
            $request->get('tagId'),
        ));

        return new Response('', Response::HTTP_OK);
    }
}
