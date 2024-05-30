<?php declare(strict_types=1);

namespace AdnanMula\Cards\Entrypoint\Controller\Shared\Admin;

use AdnanMula\Cards\Application\Command\Keyforge\Stat\General\GenerateGeneralStatsCommand;
use AdnanMula\Cards\Application\Command\Keyforge\Stat\User\GenerateUserStatsCommand;
use AdnanMula\Cards\Application\Query\Keyforge\User\GetUsersQuery;
use AdnanMula\Cards\Application\Query\User\Account\GetPendingAccountsQuery;
use AdnanMula\Cards\Domain\Model\Keyforge\Stat\ValueObject\KeyforgeStatCategory;
use AdnanMula\Cards\Domain\Model\Shared\ValueObject\UserRole;
use AdnanMula\Cards\Entrypoint\Controller\Shared\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class AdminController extends Controller
{
    public function form(Request $request): Response
    {
        $this->assertIsLogged(UserRole::ROLE_ADMIN);

        $accountsPending = $this->extractResult($this->bus->dispatch(new GetPendingAccountsQuery()));
        $users = $this->extractResult($this->bus->dispatch(new GetUsersQuery(null, null, false, false)));

        return $this->render('Shared/Admin/admin.html.twig', [
            'accountsPending' => $accountsPending,
            'users' => $users,
        ]);
    }

    public function regenerateProjection(Request $request): Response
    {
        $this->assertIsLogged(UserRole::ROLE_ADMIN);

        if ($request->request->get('projection') === KeyforgeStatCategory::HOME_GENERAL_DATA->value) {
            $this->bus->dispatch(new GenerateGeneralStatsCommand());
        }

        if ($request->request->get('projection') === KeyforgeStatCategory::USER_PROFILE->value) {
            $this->bus->dispatch(new GenerateUserStatsCommand($request->request->get('reference')));
        }

        return new JsonResponse(['result' => 'ok'], Response::HTTP_OK);
    }
}
