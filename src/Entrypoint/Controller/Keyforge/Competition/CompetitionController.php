<?php declare(strict_types=1);

namespace AdnanMula\Cards\Entrypoint\Controller\Keyforge\Competition;

use AdnanMula\Cards\Application\Command\Keyforge\Competition\Create\CreateCompetitionCommand;
use AdnanMula\Cards\Application\Command\Keyforge\Competition\Finish\FinishCompetitionCommand;
use AdnanMula\Cards\Application\Command\Keyforge\Competition\Start\StartCompetitionCommand;
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
            try {
                $this->bus->dispatch(new CreateCompetitionCommand(
                    $request->request->get('name'),
                    $request->request->get('type'),
                    $request->request->get('fixtures_type'),
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

            return $this->render(
                'Keyforge/Competition/list_competitions.html.twig',
                [
                    'result' => 'Torneo creado',
                    'success' => true,
                ],
            );
        }

        throw new MethodNotAllowedException([]);
    }

    public function start(Request $request): Response
    {
        $this->getUserWithRole(UserRole::ROLE_KEYFORGE);

        $this->bus->dispatch(new StartCompetitionCommand(
            $request->get('competitionId'),
            $request->get('date', new \DateTimeImmutable()->format('Y-m-d')),
        ));

        return new Response('', Response::HTTP_OK);
    }

    public function finish(Request $request): Response
    {
        $this->getUserWithRole(UserRole::ROLE_KEYFORGE);

        $this->bus->dispatch(new FinishCompetitionCommand(
            $request->get('competitionId'),
            $request->get('winnerId'),
            $request->get('date'),
        ));

        return new Response('', Response::HTTP_OK);
    }
}
