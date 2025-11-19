<?php declare(strict_types=1);

namespace AdnanMula\Cards\Entrypoint\Controller\Shared\User;

use AdnanMula\Cards\Application\Command\Shared\User\Update\UpdateUserCommand;
use AdnanMula\Cards\Domain\Model\Shared\User;
use AdnanMula\Cards\Domain\Model\Shared\ValueObject\Locale;
use AdnanMula\Cards\Domain\Model\Shared\ValueObject\UserRole;
use AdnanMula\Cards\Entrypoint\Controller\Shared\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

final class UserSettingsController extends Controller
{
    public function __invoke(Request $request): Response
    {
        $this->assertIsLogged(UserRole::ROLE_BASIC);
        $error = null;

        if ($request->getMethod() === Request::METHOD_POST) {
            if (false === $this->isCsrfTokenValid('user_settings_update', $request->get('_csrf_token'))) {
                throw new \Exception('Invalid CSRF token');
            }

            /** @var User $user */
            $user = $this->security->getUser();
            $newPassword = $request->request->get('settingsPassword');
            $locale = (string) $request->request->get('settingsLocale');
            $dokName = $request->request->get('settingsDokName');
            $tcoName = $request->request->get('settingsTcoName');

            try {
                $this->bus->dispatch(new UpdateUserCommand(
                    $user->id()->value(),
                    '' === $newPassword ? null : (string) $newPassword,
                    $locale,
                    '' === $dokName ? null : (string) $dokName,
                    '' === $tcoName ? null : (string) $tcoName,
                ));

                $this->setLocale(Locale::from($locale));
            } catch (\Throwable $exception) {
                $error = $exception->getMessage();
            }

            if (null === $error && '' !== $newPassword && null !== $newPassword) {
                return $this->render('Shared/Login/login.html.twig', ['last_username' => $user->getUserIdentifier(), 'error' => null]);
            }

            return $this->redirectToRoute('user_settings');
        }

        return $this->render('Shared/User/user_settings.html.twig', ['error' => $error]);
    }
}
