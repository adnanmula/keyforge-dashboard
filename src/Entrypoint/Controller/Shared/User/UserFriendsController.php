<?php declare(strict_types=1);

namespace AdnanMula\Cards\Entrypoint\Controller\Shared\User;

use AdnanMula\Cards\Application\Command\Shared\User\FriendAccept\AcceptFriendCommand;
use AdnanMula\Cards\Application\Command\Shared\User\FriendAdd\AddFriendCommand;
use AdnanMula\Cards\Application\Command\Shared\User\FriendRemove\RemoveFriendCommand;
use AdnanMula\Cards\Application\Query\User\Friend\GetUserFriendsQuery;
use AdnanMula\Cards\Domain\Model\Shared\Exception\UserNotExistsException;
use AdnanMula\Cards\Domain\Model\Shared\User;
use AdnanMula\Cards\Domain\Model\Shared\ValueObject\UserRole;
use AdnanMula\Cards\Entrypoint\Controller\Shared\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

final class UserFriendsController extends Controller
{
    public function __invoke(Request $request): Response
    {
        $this->assertIsLogged(UserRole::ROLE_BASIC);
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

        $friends = $this->extractResult($this->bus->dispatch(new GetUserFriendsQuery($user->id()->value())));

        return $this->render('Shared/User/user_friends.html.twig', [
            'error' => $error,
            'friends' => $friends['all'],
            'requestsReceived' => $friends['received'],
            'requestsSent' => $friends['sent'],
            'requestsAccepted' => $friends['accepted'],
        ]);
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
        $this->bus->dispatch(new RemoveFriendCommand($user->id()->value(), $request->get('friendId')));
    }
}
