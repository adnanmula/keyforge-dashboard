<?php declare(strict_types=1);

namespace AdnanMula\Cards\Application\Command\Shared\User\ApproveAccount;

use AdnanMula\Cards\Domain\Model\Keyforge\Deck\KeyforgeDeckTag;
use AdnanMula\Cards\Domain\Model\Keyforge\Deck\KeyforgeTagRepository;
use AdnanMula\Cards\Domain\Model\Keyforge\User\KeyforgeUser;
use AdnanMula\Cards\Domain\Model\Keyforge\User\KeyforgeUserRepository;
use AdnanMula\Cards\Domain\Model\Shared\Exception\UserNotExistsException;
use AdnanMula\Cards\Domain\Model\Shared\UserRepository;
use AdnanMula\Cards\Domain\Model\Shared\ValueObject\Locale;
use AdnanMula\Cards\Domain\Model\Shared\ValueObject\TagStyle;
use AdnanMula\Cards\Domain\Model\Shared\ValueObject\TagType;
use AdnanMula\Cards\Domain\Model\Shared\ValueObject\TagVisibility;
use AdnanMula\Cards\Domain\Model\Shared\ValueObject\UserRole;
use AdnanMula\Cards\Domain\Model\Shared\ValueObject\Uuid;
use AdnanMula\Cards\Shared\LocalizedString;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;

final readonly class ApproveAccountCommandHandler
{
    public function __construct(
        private UserRepository $repository,
        private KeyforgeUserRepository $kfUserRepository,
        private KeyforgeTagRepository $kfTagsRepository,
    ) {}

    public function __invoke(ApproveAccountCommand $command): void
    {
        $user = $this->repository->byId($command->user);

        if (null === $user) {
            throw new UserNotExistsException();
        }

        if ($user->getRoles() !== [UserRole::ROLE_BASIC->value]) {
            throw new UnsupportedUserException();
        }

        if ($command->approve) {
            $user->setRole(UserRole::ROLE_KEYFORGE);

            $this->kfUserRepository->save(KeyforgeUser::create($user->id(), $user->name(), null));

            $this->kfTagsRepository->save(new KeyforgeDeckTag(
                Uuid::v4(),
                LocalizedString::fromArray(
                    [
                        Locale::es_ES->value => 'Fav',
                        Locale::en_GB->value => 'Fav',
                    ],
                ),
                TagVisibility::PRIVATE,
                TagStyle::from([
                    TagStyle::COLOR_BG => '#e8db1e',
                    TagStyle::COLOR_TEXT => '#000000',
                    TagStyle::COLOR_OUTLINE => '#e8db1e',
                ]),
                TagType::CUSTOM,
                false,
                $user->id(),
            ));
        } else {
            $user->setRole(UserRole::ROLE_REJECTED_ACCOUNT);
        }

        $this->repository->save($user);
    }
}
