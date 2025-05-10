<?php declare(strict_types=1);

namespace AdnanMula\Cards\Entrypoint\Controller\Shared\Login;

use AdnanMula\Cards\Application\Command\Shared\User\Create\CreateUserCommand;
use AdnanMula\Cards\Domain\Model\Shared\ValueObject\Locale;
use AdnanMula\Cards\Entrypoint\Controller\Shared\Controller;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Translation\LocaleSwitcher;
use Symfony\Contracts\Translation\TranslatorInterface;

final class RegisterController extends Controller
{
    public function __construct(
        MessageBusInterface $bus,
        Security $security,
        LocaleSwitcher $localeSwitcher,
        TranslatorInterface $translator,
        LoggerInterface $logger,
        private bool $registrationClosed,
    ) {
        parent::__construct($bus, $security, $localeSwitcher, $translator, $logger);
    }

    public function __invoke(Request $request): Response
    {
        if ($request->getMethod() === Request::METHOD_POST) {
            $error = null;

            if ($this->registrationClosed) {
                $error = 'Registration is currently closed. If you already have an account you can log in';
            } else {
                try {
                    $this->bus->dispatch(new CreateUserCommand(
                        $request->request->get('_username'),
                        $request->request->get('_password'),
                        Locale::es_ES->value,
                        ['ROLE_BASIC'],
                    ));
                } catch (\Throwable $exception) {
                    $error = $exception->getMessage();
                }
            }

            if (null !== $error) {
                return $this->render('Shared/Login/register.html.twig', [
                    'error' => $error,
                    'registrationClosed' => $this->registrationClosed,
                ]);
            }

            return $this->render('Shared/Login/login.html.twig', ['error' => null, 'last_username' => $request->request->get('_username')]);
        }

        return $this->render('Shared/Login/register.html.twig', [
            'error' => null,
            'registrationClosed' => $this->registrationClosed,
        ]);
    }
}
