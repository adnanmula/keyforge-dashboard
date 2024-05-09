<?php declare(strict_types=1);

namespace AdnanMula\Cards\Application\Query\User\Account;

use AdnanMula\Cards\Domain\Model\Shared\UserRepository;
use AdnanMula\Cards\Domain\Model\Shared\ValueObject\UserRole;

final readonly class GetPendingAccountsQueryHandler
{
    public function __construct(
        private UserRepository $repository,
    ) {}

    public function __invoke(GetPendingAccountsQuery $query): array
    {
        return $this->repository->byRole(UserRole::ROLE_BASIC);
    }
}
