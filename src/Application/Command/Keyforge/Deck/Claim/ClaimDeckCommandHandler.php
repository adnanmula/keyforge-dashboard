<?php declare(strict_types=1);

namespace AdnanMula\Cards\Application\Command\Keyforge\Deck\Claim;

use AdnanMula\Cards\Domain\Model\Keyforge\KeyforgeDeckRepository;
use AdnanMula\Cards\Domain\Model\Shared\UserRepository;

final class ClaimDeckCommandHandler
{
    public function __construct(
        private KeyforgeDeckRepository $repository,
        private UserRepository $userRepository,
    ) {}

    public function __invoke(ClaimDeckCommand $command): void
    {
        $deck = $this->repository->byId($command->deckId());

        if (null === $deck) {
            throw new \Exception('Deck does not exists');
        }

        if (null !== $deck->owner()) {
            throw new \Exception('Already claimed');
        }

        $user = $this->userRepository->byId($command->userId());

        if (null === $user) {
            throw new \Exception('User not exists');
        }

        $deck->updateOwner($command->userId());

        $this->repository->save($deck);
    }
}
