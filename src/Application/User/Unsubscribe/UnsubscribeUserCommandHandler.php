<?php declare(strict_types=1);

namespace AdnanMula\Cards\Application\User\Unsubscribe;

use AdnanMula\Cards\Domain\Service\User\UserRemover;

final class UnsubscribeUserCommandHandler
{
    private UserRemover $remover;

    public function __construct(UserRemover $remover)
    {
        $this->remover = $remover;
    }

    public function __invoke(UnsubscribeUserCommand $command): void
    {
        $this->remover->execute($command->reference());
    }
}
