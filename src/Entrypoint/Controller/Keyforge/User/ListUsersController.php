<?php declare(strict_types=1);

namespace AdnanMula\Cards\Entrypoint\Controller\Keyforge\User;

use AdnanMula\Cards\Application\Query\Keyforge\User\GetUsersQuery;
use AdnanMula\Cards\Domain\Model\Shared\User;
use AdnanMula\Cards\Entrypoint\Controller\Shared\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

final class ListUsersController extends Controller
{
    public function __invoke(Request $request): Response
    {
        $this->assertIsLogged();
        /** @var User $user */
        $user = $this->getUser();

        $users = $this->extractResult(
            $this->bus->dispatch(new GetUsersQuery(
                null,
                null,
                true,
                true,
                $request->get('onlyFriends') !== null,
                $user->id()->value(),
            )),
        );

        return $this->render(
            'Keyforge/User/list_users.html.twig',
            [
                'users' => $users,
                'onlyFriends' => $request->get('onlyFriends') !== null,
            ],
        );
    }
}
