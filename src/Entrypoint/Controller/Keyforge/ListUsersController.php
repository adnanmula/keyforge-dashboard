<?php declare(strict_types=1);

namespace AdnanMula\Cards\Entrypoint\Controller\Keyforge;

use AdnanMula\Cards\Application\Keyforge\User\GetUsersCommand;
use AdnanMula\Cards\Entrypoint\Controller\Shared\QueryController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

final class ListUsersController extends QueryController
{
    public function __invoke(Request $request): Response
    {
        $users = $this->extractResult(
            $this->bus->dispatch(new GetUsersCommand(0, 1000)),
        );

        return $this->render(
            'Keyforge/list_users.html.twig',
            ['users' => $users],
        );
    }
}
