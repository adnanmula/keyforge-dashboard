<?php declare(strict_types=1);

namespace AdnanMula\Cards\Entrypoint\Controller\Keyforge\Competition;

use AdnanMula\Cards\Application\Command\Keyforge\Competition\Create\CreateCompetitionCommand;
use AdnanMula\Cards\Application\Query\Keyforge\User\GetUsersQuery;
use AdnanMula\Cards\Domain\Model\Keyforge\KeyforgeUser;
use AdnanMula\Cards\Entrypoint\Controller\Shared\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class CreateCompetitionController extends Controller
{
        public function __invoke(Request $request): Response
        {
            $users = $this->extractResult(
                $this->bus->dispatch(new GetUsersQuery(null, null, false, false)),
            );

            $users = \array_map(static fn (KeyforgeUser $user) => ['id' => $user->id()->value(), 'name' => $user->name()], $users);

            if ($request->getMethod() === Request::METHOD_GET) {
                return $this->render(
                    'Keyforge/Competition/create_competition.html.twig',
                    [
                        'users' => $users,
                        'result' => false,
                        'success' => null,
                    ],
                );
            }

            if ($request->getMethod() === Request::METHOD_POST) {
                try {
                    $this->bus->dispatch(new CreateCompetitionCommand(
                        \str_replace(' ', '_', \strtoupper($request->request->get('name'))),
                        $request->request->get('name'),
                        $request->request->get('type'),
                        $request->request->all()['users'] ?? [],
                        $request->request->get('description'),
                    ));

                    return $this->render(
                        'Keyforge/Competition/create_competition.html.twig',
                        [
                            'users' => $users,
                            'result' => 'Torneo creado',
                            'success' => true,
                        ],
                    );
                } catch (\Throwable $exception) {
                    return $this->render(
                        'Keyforge/Competition/create_competition.html.twig',
                        [
                            'users' => $users,
                            'result' => $exception->getMessage(),
                            'success' => false,
                        ],
                    );
                }
            }

            throw new \InvalidArgumentException('Error');
        }
}