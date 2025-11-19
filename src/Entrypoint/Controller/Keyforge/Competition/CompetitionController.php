<?php declare(strict_types=1);

namespace AdnanMula\Cards\Entrypoint\Controller\Keyforge\Competition;

use AdnanMula\Cards\Application\Command\Keyforge\Competition\Create\CreateCompetitionCommand;
use AdnanMula\Cards\Application\Command\Keyforge\Competition\Finish\FinishCompetitionCommand;
use AdnanMula\Cards\Application\Command\Keyforge\Competition\Join\JoinCompetitionCommand;
use AdnanMula\Cards\Application\Command\Keyforge\Competition\Leave\LeaveCompetitionCommand;
use AdnanMula\Cards\Application\Command\Keyforge\Competition\Start\StartCompetitionCommand;
use AdnanMula\Cards\Application\Query\Keyforge\User\GetUsersQuery;
use AdnanMula\Cards\Domain\Model\Keyforge\User\KeyforgeUser;
use AdnanMula\Cards\Domain\Model\Shared\ValueObject\UserRole;
use AdnanMula\Cards\Entrypoint\Controller\Shared\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Exception\MethodNotAllowedException;

class CompetitionController extends Controller
{
    public function create(Request $request): Response
    {
        $user = $this->getUserWithRole(UserRole::ROLE_KEYFORGE);

        if ($request->getMethod() === Request::METHOD_GET) {
            return $this->render(
                'Keyforge/Competition/create_competition.html.twig',
                [
                    'result' => false,
                    'success' => null,
                ],
            );
        }

        if ($request->getMethod() === Request::METHOD_POST) {
            if (false === $this->isCsrfTokenValid('keyforge_competition_create', $request->get('_csrf_token'))) {
                throw new \Exception('Invalid CSRF token');
            }

            try {
                $this->bus->dispatch(new CreateCompetitionCommand(
                    $request->request->get('name'),
                    $request->request->get('type'),
                    $request->request->get('fixtures_type'),
                    [$user->id()->value()],
                    [$user->id()->value()],
                    $request->request->get('description'),
                    $request->request->get('visibility'),
                ));
            } catch (\Throwable $exception) {
                return $this->render(
                    'Keyforge/Competition/create_competition.html.twig',
                    [
                        'result' => $exception->getMessage(),
                        'success' => false,
                    ],
                );
            }

            return $this->redirectToRoute('keyforge_competition_list', ['success' => true]);
        }

        throw new MethodNotAllowedException([]);
    }

    public function list(Request $request): Response
    {
        $this->assertIsLogged();

        $users = $this->extractResult(
            $this->bus->dispatch(new GetUsersQuery(null, null, false, false)),
        );

        return $this->render('Keyforge/Competition/list_competitions.html.twig', [
            'users' => \array_map(static fn (KeyforgeUser $user) => $user->jsonSerialize(), $users),
            'success' => $request->get('success', false),
            'result' => $request->get('success') ? 'Torneo creado' : false,
        ]);
    }

    public function start(Request $request): Response
    {
        $this->getUserWithRole(UserRole::ROLE_KEYFORGE);

        if (false === $this->isCsrfTokenValid('keyforge_competition_start', $request->get('_csrf_token'))) {
            throw new \Exception('Invalid CSRF token');
        }

        $this->bus->dispatch(new StartCompetitionCommand(
            $request->get('competitionId'),
            $request->get('date', new \DateTimeImmutable()->format('Y-m-d')),
        ));

        return new Response('', Response::HTTP_OK);
    }

    public function finish(Request $request): Response
    {
        $this->getUserWithRole(UserRole::ROLE_KEYFORGE);

        if (false === $this->isCsrfTokenValid('keyforge_competition_game_finish', $request->get('_csrf_token'))) {
            throw new \Exception('Invalid CSRF token');
        }

        $this->bus->dispatch(new FinishCompetitionCommand(
            $request->get('competitionId'),
            $request->get('winnerId'),
            $request->get('date'),
        ));

        return new Response('', Response::HTTP_OK);
    }

    public function join(Request $request): Response
    {
        $this->getUserWithRole(UserRole::ROLE_KEYFORGE);

        if (false === $this->isCsrfTokenValid('keyforge_competition_join', $request->get('_csrf_token'))) {
            throw new \Exception('Invalid CSRF token');
        }

        $this->bus->dispatch(new JoinCompetitionCommand(
            $request->get('id'),
        ));

        return new Response('', Response::HTTP_OK);
    }

    public function leave(Request $request): Response
    {
        $this->getUserWithRole(UserRole::ROLE_KEYFORGE);

        if (false === $this->isCsrfTokenValid('keyforge_competition_leave', $request->get('_csrf_token'))) {
            throw new \Exception('Invalid CSRF token');
        }

        $this->bus->dispatch(new LeaveCompetitionCommand(
            $request->get('id'),
        ));

        return new Response('', Response::HTTP_OK);
    }
}
