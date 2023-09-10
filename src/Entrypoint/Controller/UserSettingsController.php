<?php declare(strict_types=1);

namespace AdnanMula\Cards\Entrypoint\Controller;

use AdnanMula\Cards\Application\Command\Shared\User\Update\UpdateUserCommand;
use AdnanMula\Cards\Domain\Model\Shared\User;
use AdnanMula\Cards\Domain\Model\Shared\ValueObject\Locale;
use AdnanMula\Cards\Entrypoint\Controller\Shared\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

final class UserSettingsController extends Controller
{
    public function __invoke(Request $request): Response
    {
        $this->assertIsLogged();
        $error = null;

        if ($request->getMethod() === Request::METHOD_POST) {
            /** @var User $user */
            $user = $this->security->getUser();
            $newPassword = $request->request->get('settingsPassword');
            $locale = $request->request->get('settingsLocale');

            try {
                $this->bus->dispatch(new UpdateUserCommand(
                    $user->id()->value(),
                    '' === $newPassword ? null : $newPassword,
                    $locale,
                ));

                $this->setLocale(Locale::from($locale));
            } catch (\Throwable $exception) {
                $error = $exception->getMessage();
            }

            if (null === $error && '' !== $newPassword && null !== $newPassword) {
                return $this->render('login.html.twig', ['last_username' => $user->getUserIdentifier(), 'error' => null]);
            }
        }

        return $this->render('user_settings.html.twig', ['error' => $error]);
    }
}
