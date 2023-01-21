<?php declare(strict_types=1);

namespace AdnanMula\Cards\Entrypoint\Controller\Shared;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Messenger\Stamp\HandledStamp;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

class Controller extends AbstractController
{
    public function __construct(protected MessageBusInterface $bus, protected Security $security) {}

    final protected function assertIsLogged(): void
    {
        if (false === $this->security->isGranted('ROLE_KEYFORGE')) {
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
}
