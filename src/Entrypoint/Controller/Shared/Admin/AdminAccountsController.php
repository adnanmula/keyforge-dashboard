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

        if (Request::METHOD_POST === $request->getMethod()) {
            $this->bus->dispatch(new ApproveAccountCommand($request->request->get('id')));

            return new Response('', Response::HTTP_OK);
        }

        if (Request::METHOD_DELETE === $request->getMethod()) {
            throw new \Exception('Operation not supported');
        }

        throw new \Exception('Operation not supported');
    }
}
