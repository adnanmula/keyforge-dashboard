<?php declare(strict_types=1);

namespace AdnanMula\Cards\Entrypoint\Controller\Keyforge\Deck\UpdateOwnership;

use AdnanMula\Cards\Domain\Model\Keyforge\Deck\KeyforgeDeckRepository;
use AdnanMula\Cards\Domain\Model\Shared\User;
use AdnanMula\Cards\Domain\Model\Shared\ValueObject\Uuid;
use AdnanMula\Cards\Entrypoint\Controller\Shared\Controller;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Translation\LocaleSwitcher;
use Symfony\Contracts\Translation\TranslatorInterface;

final class UpdateDeckOwnershipController extends Controller
{
    public function __construct(
        MessageBusInterface $bus,
        Security $security,
        LocaleSwitcher $localeSwitcher,
        TranslatorInterface $translator,
        LoggerInterface $logger,
        private KeyforgeDeckRepository $deckRepository,
    ) {
        parent::__construct($bus, $security, $localeSwitcher, $translator, $logger);
    }

    public function __invoke(Request $request, string $id): Response
    {
        $this->assertIsLogged();

        /** @var User $user */
        $user = $this->security->getUser();

        if (false === Uuid::isValid($id)) {
            throw new \InvalidArgumentException('Invalid deck id');
        }

        if ($request->getMethod() === Request::METHOD_POST) {
            if (false === $this->isCsrfTokenValid('keyforge_ownership_update_add', $request->get('_csrf_token'))) {
                throw new \Exception('Invalid CSRF token');
            }

            $this->deckRepository->addOwner(Uuid::from($id), $user->id());
        }

        if ($request->getMethod() === Request::METHOD_DELETE) {
            if (false === $this->isCsrfTokenValid('keyforge_ownership_update_remove', $request->get('_csrf_token'))) {
                throw new \Exception('Invalid CSRF token');
            }

            $this->deckRepository->removeOwner(Uuid::from($id), $user->id());
        }

        return new Response('', Response::HTTP_OK);
    }
}
