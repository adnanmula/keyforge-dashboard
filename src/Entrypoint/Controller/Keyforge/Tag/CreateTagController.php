<?php declare(strict_types=1);

namespace AdnanMula\Cards\Entrypoint\Controller\Keyforge\Tag;

use AdnanMula\Cards\Application\Command\Keyforge\Tag\Create\CreateTagCommand;
use AdnanMula\Cards\Domain\Model\Shared\ValueObject\TagType;
use AdnanMula\Cards\Domain\Model\Shared\ValueObject\TagVisibility;
use AdnanMula\Cards\Domain\Model\Shared\ValueObject\Uuid;
use AdnanMula\Cards\Entrypoint\Controller\Shared\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

final class CreateTagController extends Controller
{
    public function __invoke(Request $request): Response
    {
        $this->assertIsLogged();
        $this->validateCsrfToken('keyforge_deck_tag_create', $request->get('_csrf_token'));

        $this->bus->dispatch(new CreateTagCommand(
            Uuid::v4()->value(),
            $request->get('name'),
            TagVisibility::PRIVATE->value,
            TagType::CUSTOM->value,
            $request->get('styleBg'),
            $request->get('styleText'),
            $request->get('styleOutline'),
            $request->get('deckId'),
        ));

        return new Response('', Response::HTTP_OK);
    }
}
