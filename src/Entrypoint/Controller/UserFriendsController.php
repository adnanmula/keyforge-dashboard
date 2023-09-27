<?php declare(strict_types=1);

namespace AdnanMula\Cards\Entrypoint\Controller;

use AdnanMula\Cards\Application\Command\Shared\User\AcceptFriend\AcceptFriendCommand;
use AdnanMula\Cards\Application\Command\Shared\User\AddFriend\AddFriendCommand;
use AdnanMula\Cards\Domain\Model\Shared\Exception\UserNotExistsException;
use AdnanMula\Cards\Domain\Model\Shared\User;
use AdnanMula\Cards\Domain\Model\Shared\UserRepository;
use AdnanMula\Cards\Domain\Model\Shared\ValueObject\Uuid;
use AdnanMula\Cards\Entrypoint\Controller\Shared\Controller;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Translation\LocaleSwitcher;
use Symfony\Contracts\Translation\TranslatorInterface;

final class UserFriendsController extends Controller
{
    public function __construct(
        private readonly UserRepository $userRepository,
        MessageBusInterface $bus,
        Security $security,
        LocaleSwitcher $localeSwitcher,
        TranslatorInterface $translator,
    ) {
        parent::__construct($bus, $security, $localeSwitcher, $translator);
    }

    public function __invoke(Request $request): Response
    {
        $this->assertIsLogged();
        /** @var User $user */
        $user = $this->getUser();
        $error = null;

        if ($request->getMethod() === Request::METHOD_PATCH) {
            $error = $this->acceptFriend($request, $user);
        }

        if ($request->getMethod() === Request::METHOD_DELETE) {
            $this->removeFriend($request, $user);

            return new Response();
        }

        if ($request->getMethod() === Request::METHOD_POST) {
            $error = $this->addFriend($request, $user);
        }

        $friends = $this->userRepository->friends($user->id());

        $requestsReceived = \array_filter($friends, static fn (array $r) => $r['is_request'] === true && $r['friend_id'] === $user->id()->value());
        $requestsSent = \array_filter($friends, static fn (array $r) => $r['is_request'] === true && $r['id'] === $user->id()->value());
        $requestsAccepted = \array_filter($friends, static fn (array $r) => $r['is_request'] === false && $r['id'] === $user->id()->value());

        return $this->render('user_friends.html.twig', [
            'error' => $error,
            'friends' => $friends,
            'requestsReceived' => $requestsReceived,
            'requestsSent' => $requestsSent,
            'requestsAccepted' => $requestsAccepted,
        ]);
    }

    private function acceptFriend(Request $request, User $user): ?string
    {
        try {
            $this->bus->dispatch(new AcceptFriendCommand($user->id()->value(), $request->get('friendId')));
        } catch (UserNotExistsException) {
            return $this->translator->trans('exception.user_not_exists');
        } catch (\Throwable $e) {
            return $e->getMessage();
        }

        return null;
    }

    private function removeFriend(Request $request, User $user): void
    {
        $this->userRepository->removeFriend($user->id(), Uuid::from($request->get('friendId')));
        $this->userRepository->removeFriend(Uuid::from($request->get('friendId')), $user->id());
    }

    private function addFriend(Request $request, User $user): ?string
    {
        try {
            $this->bus->dispatch(new AddFriendCommand($user->id()->value(), $request->get('friendName')));
        } catch (UserNotExistsException) {
            return $this->translator->trans('exception.user_not_exists');
        } catch (\Throwable $e) {
            return $e->getMessage();
        }

        return null;
    }
}
