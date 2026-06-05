<?php declare(strict_types=1);

namespace AdnanMula\Cards\Entrypoint\Controller\Keyforge\User;

use AdnanMula\Cards\Application\Query\Keyforge\User\GetUsersQuery;
use AdnanMula\Cards\Domain\Model\Shared\ValueObject\UserRole;
use AdnanMula\Cards\Entrypoint\Controller\Shared\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

class GetUsersController extends Controller
{
    public function __invoke(Request $request): JsonResponse
    {
        $this->getUserWithRole(UserRole::ROLE_KEYFORGE);

        $users = $this->extractResult(
            $this->bus->dispatch(new GetUsersQuery(
                0,
                $request->query->get('length', 20),
                $request->query->get('withGames', false) === 'true',
                $request->query->get('withExternal', false) === 'true',
                $request->query->get('onlyFriends', false) === 'true',
                $request->query->get('userId'),
                $request->query->get('name'),
            )),
        );

        $response = [
            'data' => $users,
            'draw' => (int) $request->query->get('draw'),
            'recordsFiltered' => count($users),
            'recordsTotal' => count($users),
        ];

        return new JsonResponse($response);
    }
}
