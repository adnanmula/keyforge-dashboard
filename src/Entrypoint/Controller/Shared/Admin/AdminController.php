<?php declare(strict_types=1);

namespace AdnanMula\Cards\Entrypoint\Controller\Shared\Admin;

use AdnanMula\Cards\Application\Query\User\Account\GetPendingAccountsQuery;
use AdnanMula\Cards\Domain\Model\Shared\ValueObject\UserRole;
use AdnanMula\Cards\Entrypoint\Controller\Shared\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class AdminController extends Controller
{
    public function __invoke(Request $request): Response
    {
        $this->assertIsLogged(UserRole::ROLE_ADMIN);

        $accountsPending = $this->extractResult($this->bus->dispatch(new GetPendingAccountsQuery()));

        return $this->render('Shared/Admin/admin.html.twig', [
            'accountsPending' => $accountsPending,
        ]);
    }
}
