<?php declare(strict_types=1);

namespace AdnanMula\Cards\Application\Keyforge\Get;

use AdnanMula\Cards\Domain\Model\Keyforge\KeyforgeDeck;
use AdnanMula\Cards\Domain\Model\User\UserRepository;

final class GetKeyforgeUsersQueryHandler
{
    public function __construct(
        private UserRepository $repository
    ) {}

    /** @return array<KeyforgeDeck> */
    public function __invoke(GetKeyforgeUsersQuery $query): array
    {
        return $this->repository->all();
    }
}
