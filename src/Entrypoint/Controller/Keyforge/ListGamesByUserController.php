<?php declare(strict_types=1);

namespace AdnanMula\Cards\Entrypoint\Controller\Keyforge;

use AdnanMula\Cards\Application\Keyforge\UserStats\GetUserStatsQuery;
use AdnanMula\Cards\Entrypoint\Controller\Shared\QueryController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

final class ListGamesByUserController extends QueryController
{
    public function __invoke(Request $request, string $userId): Response
    {
        $games = $this->extractResult(
            $this->bus->dispatch(new GetUserStatsQuery($userId)),
        );

        return $this->render('Keyforge/list_games.html.twig', ['games' => $games]);
    }
}
