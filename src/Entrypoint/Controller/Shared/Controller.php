<?php declare(strict_types=1);

namespace AdnanMula\Cards\Entrypoint\Controller\Shared;

use AdnanMula\Cards\Domain\Model\Shared\User;
use AdnanMula\Cards\Domain\Model\Shared\ValueObject\Locale;
use AdnanMula\Cards\Domain\Model\Shared\ValueObject\UserRole;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Messenger\Stamp\HandledStamp;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\Translation\LocaleSwitcher;
use Symfony\Contracts\Translation\TranslatorInterface;

class Controller extends AbstractController
{
    public function __construct(
        protected MessageBusInterface $bus,
        protected Security $security,
        protected LocaleSwitcher $localeSwitcher,
        protected TranslatorInterface $translator,
        protected LoggerInterface $logger,
    ) {
        $this->setLocaleToUser();
    }

    final protected function getUser(): ?User
    {
        /** @var ?User $user */
        $user = parent::getUser();

        return $user;
    }

    final protected function getUserWithRole(UserRole $role): User
    {
        /** @var ?User $user */
        $user = parent::getUser();

        if (null === $user || false === $this->security->isGranted($role->value)) {
            throw new AccessDeniedException();
        }

        return $user;
    }

    final protected function assertIsLogged(UserRole $role = UserRole::ROLE_KEYFORGE): void
    {
        if (false === $this->security->isGranted($role->value)) {
            throw new AccessDeniedException();
        }
    }

    final protected function extractResult(mixed $message): mixed
    {
        $stamp = $message->last(HandledStamp::class);

        if (null === $stamp) {
            return null;
        }

        return $stamp->getResult();
    }

    final protected function setLocale(Locale $locale): void
    {
        $this->localeSwitcher->setLocale($locale->value);
    }

    final protected function setLocaleToUser(): void
    {
        /** @var ?User $user */
        $user = $this->security->getUser();

        if (null === $user) {
            return;
        }

        $this->localeSwitcher->setLocale($user->locale()->value);
    }
}
