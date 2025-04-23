<?php declare(strict_types=1);

namespace AdnanMula\Cards\Entrypoint\Controller\Keyforge\Game\Create;

use AdnanMula\Cards\Application\Command\Keyforge\Game\Create\CreateGameCommand;
use AdnanMula\Cards\Domain\Model\Shared\ValueObject\UserRole;
use AdnanMula\Cards\Entrypoint\Controller\Shared\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

final class CreateGameController extends Controller
{
    public function __invoke(Request $request): Response
    {
        $this->getUserWithRole(UserRole::ROLE_KEYFORGE);

        if ($request->getMethod() === Request::METHOD_GET) {
            return $this->render(
                'Keyforge/Game/Create/create_game.html.twig',
                [
                    'result' => false,
                    'success' => null,
                ],
            );
        }

        if ($request->getMethod() === Request::METHOD_POST) {
            try {
                $this->bus->dispatch(new CreateGameCommand(
                    $request->request->get('winner'),
                    $request->request->get('winnerDeck'),
                    $request->request->get('winnerChains'),
                    $request->request->get('loser'),
                    $request->request->get('loserDeck'),
                    $request->request->get('loserChains'),
                    $request->request->get('loserScore'),
                    $request->request->get('firstTurn') === '' ? null : $request->request->get('firstTurn'),
                    $request->request->get('date'),
                    $request->request->get('competition'),
                    $request->request->get('notes'),
                    $request->request->get('log') === '' ? null : $request->request->get('log'),
                ));

                return $this->render(
                    'Keyforge/Game/Create/create_game.html.twig',
                    [
                        'result' => $this->translator->trans('menu.game_saved'),
                        'success' => true,
                    ],
                );
            } catch (\Throwable $exception) {
                return $this->render(
                    'Keyforge/Game/Create/create_game.html.twig',
                    [
                        'result' => $exception->getMessage(),
                        'success' => false,
                    ],
                );
            }
        }

        throw new \InvalidArgumentException('Error');
    }
}
