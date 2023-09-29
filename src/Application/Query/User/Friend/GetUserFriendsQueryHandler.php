<?php declare(strict_types=1);

namespace AdnanMula\Cards\Application\Query\User\Friend;

use AdnanMula\Cards\Domain\Model\Shared\UserRepository;

final readonly class GetUserFriendsQueryHandler
{
    public function __construct(
        private UserRepository $repository,
    ) {}

    public function __invoke(GetUserFriendsQuery $query): array
    {
        $friends = $this->repository->friends($query->userId);

        $requestsReceived = \array_filter($friends, static fn (array $r) => $r['is_request'] === true && $r['friend_id'] === $query->userId->value());
        $requestsSent = \array_filter($friends, static fn (array $r) => $r['is_request'] === true && $r['id'] === $query->userId->value());
        $requestsAccepted = \array_filter($friends, static fn (array $r) => $r['is_request'] === false && $r['id'] === $query->userId->value());

        return [
            'all' => $friends,
            'received' => $requestsReceived,
            'sent' => $requestsSent,
            'accepted' => $requestsAccepted,
        ];
    }
}
