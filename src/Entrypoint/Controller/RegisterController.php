<?php declare(strict_types=1);

namespace AdnanMula\Cards\Entrypoint\Controller;

use AdnanMula\Cards\Application\Command\Shared\User\Create\CreateUserCommand;
use AdnanMula\Cards\Entrypoint\Controller\Shared\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

final class RegisterController extends Controller
{
    public function __invoke(Request $request): Response
    {
        if ($request->getMethod() === Request::METHOD_POST) {
            $error = null;

            try {
                $this->bus->dispatch(new CreateUserCommand(
                    $request->request->get('_username'),
                    $request->request->get('_password'),
                    ["ROLE_BASIC"],
                ));
            } catch (\Throwable $exception) {
                $error = $exception->getMessage();
            }

            if (null !== $error) {
                return $this->render('register.html.twig', ['error' => $error]);
            }

            return $this->render('login.html.twig', ['error' => null, 'last_username' => $request->request->get('_username')]);
        }

        return $this->render('register.html.twig', ['error' => null]);
    }
}
