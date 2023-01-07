<?php declare(strict_types=1);

namespace AdnanMula\Cards\Entrypoint\Controller\Keyforge\Competition;

use AdnanMula\Cards\Application\Command\Keyforge\Competition\Create\CreateCompetitionCommand;
use AdnanMula\Cards\Application\Query\Keyforge\User\GetUsersQuery;
use AdnanMula\Cards\Domain\Model\Keyforge\KeyforgeUser;
use AdnanMula\Cards\Entrypoint\Controller\Shared\Controller;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

class CreateCompetitionController extends Controller
{
    private Security $security;

    public function __construct(MessageBusInterface $bus, Security $security)
    {
        $this->security = $security;

        if (false === $this->security->isGranted('ROLE_KEYFORGE')) {
            throw new AccessDeniedException();
        }

        parent::__construct($bus);
    }

    public function __invoke(Request $request): Response
    {
        if (false === $this->security->isGranted('ROLE_KEYFORGE')) {
            throw new AccessDeniedException();
        }

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
                    \strtolower(\preg_replace("/[\W_]+/u", '_', $request->request->get('name'))),
                    $request->request->get('name'),
                    $request->request->get('type'),
                    $request->request->all()['users'] ?? [],
                    $request->request->get('description'),
                ));
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

            return $this->render(
                'Keyforge/Competition/list_competitions.html.twig',
                [
                    'users' => $users,
                    'result' => 'Torneo creado',
                    'success' => true,
                ],
            );
        }

        throw new \InvalidArgumentException('Error');
    }
}
