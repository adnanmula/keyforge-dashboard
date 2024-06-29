<?php declare(strict_types=1);

namespace AdnanMula\Cards\Entrypoint\Controller\Keyforge\User;

use AdnanMula\Cards\Domain\Model\Shared\UserRepository;
use AdnanMula\Cards\Domain\Model\Shared\ValueObject\UserRole;
use AdnanMula\Cards\Entrypoint\Controller\Shared\Controller;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Translation\LocaleSwitcher;
use Symfony\Contracts\Translation\TranslatorInterface;

final class UserDetailController extends Controller
{
    public function __construct(
        MessageBusInterface $bus,
        Security $security,
        LocaleSwitcher $localeSwitcher,
        TranslatorInterface $translator,
        private UserRepository $userRepository,
    ) {
        parent::__construct($bus, $security, $localeSwitcher, $translator);
    }

    public function __invoke(string $userId): Response
    {
        $this->assertIsLogged(UserRole::ROLE_KEYFORGE);

        $user = $this->getUser();

        $indexedFriends = [];

        if (null !== $user) {
            $friends = $this->userRepository->friends($user->id(), false);

            foreach ($friends as $friend) {
                $indexedFriends[$friend['id']] = $friend['sender_name'];
                $indexedFriends[$friend['friend_id']] = $friend['receiver_name'];
            }
        }

        return $this->render(
            'Keyforge/User/user_detail.html.twig',
            [
                'reference' => $userId,
                'userId' => $userId,
                'stats' => [],
                'indexed_friends' => $indexedFriends,
            ],
        );
    }
}
