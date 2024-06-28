<?php declare(strict_types=1);

namespace AdnanMula\Cards\Entrypoint\Controller\Keyforge\User;

use AdnanMula\Cards\Application\Query\Keyforge\User\GetUsersQuery;
use AdnanMula\Cards\Entrypoint\Controller\Shared\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

final class ListUsersController extends Controller
{
    public function __invoke(Request $request): Response
    {
        $this->assertIsLogged();
        $user = $this->getUser();

        $users = $this->extractResult(
            $this->bus->dispatch(new GetUsersQuery(
                null,
                null,
                true,
                false,
                $request->get('showAll') === null,
                $user->id()->value(),
            )),
        );

        return $this->render(
            'Keyforge/User/list_users.html.twig',
            [
                'users' => $users,
                'showAll' => $request->get('showAll') !== null,
            ],
        );
    }
}
