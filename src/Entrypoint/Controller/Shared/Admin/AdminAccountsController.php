<?php declare(strict_types=1);

namespace AdnanMula\Cards\Entrypoint\Controller\Shared\Admin;

use AdnanMula\Cards\Application\Command\Shared\User\ApproveAccount\ApproveAccountCommand;
use AdnanMula\Cards\Domain\Model\Shared\ValueObject\UserRole;
use AdnanMula\Cards\Entrypoint\Controller\Shared\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

final class AdminAccountsController extends Controller
{
    public function __invoke(Request $request): Response
    {
        $this->assertIsLogged(UserRole::ROLE_ADMIN);

        if (false === \in_array($request->getMethod(), [Request::METHOD_POST, Request::METHOD_DELETE], true)) {
            throw new \Exception('Operation not supported');
        }

        if (false === $this->isCsrfTokenValid('admin_manage_accounts', $request->get('_csrf_token'))) {
            throw new \Exception('Invalid CSRF token');
        }

        $this->bus->dispatch(new ApproveAccountCommand(
            $request->request->get('id'),
            $request->getMethod() === Request::METHOD_POST,
        ));

        return new Response('', Response::HTTP_OK);
    }
}
