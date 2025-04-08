<?php declare(strict_types=1);

namespace AdnanMula\Cards\Entrypoint\Controller\Keyforge\Tag;

use AdnanMula\Cards\Application\Command\Keyforge\Tag\Remove\RemoveTagCommand;
use AdnanMula\Cards\Domain\Model\Shared\ValueObject\UserRole;
use AdnanMula\Cards\Entrypoint\Controller\Shared\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

final class RemoveTagController extends Controller
{
    public function __invoke(Request $request): Response
    {
        $this->getUserWithRole(UserRole::ROLE_KEYFORGE);

        $this->bus->dispatch(new RemoveTagCommand($request->get('id')));

        return new Response('', Response::HTTP_OK);
    }
}
