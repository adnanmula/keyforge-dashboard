<?php declare(strict_types=1);

namespace AdnanMula\Cards\Entrypoint\Controller\Keyforge\Game\List;

use AdnanMula\Cards\Application\Query\Keyforge\User\GetUsersQuery;
use AdnanMula\Cards\Entrypoint\Controller\Shared\Controller;
use Symfony\Component\HttpFoundation\Response;

final class ListGamesController extends Controller
{
    public function __invoke(): Response
    {
        $user = $this->getUser();

        $users = [];

        if (null !== $user) {
            $kfUsers = $this->extractResult($this->bus->dispatch(new GetUsersQuery(
                page: null,
                pageSize: null,
                withGames: false,
                withExternal: false,
                onlyFriends: true,
                userId: $user->id()->value(),
            )));

            \usort($kfUsers, static fn ($a, $b) => $a->id()->value() === $user->id()->value() ? -1 : 1);

            foreach ($kfUsers as $kfUser) {
                $users[$kfUser->id()->value()] = $kfUser->name();
            }
        }

        return $this->render(
            'Keyforge/Game/List/list_games.html.twig',
            [
                'users' => $users,
            ],
        );
    }
}
