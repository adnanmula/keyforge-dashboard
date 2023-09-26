<?php declare(strict_types=1);

namespace AdnanMula\Cards\Entrypoint\Controller;

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

        if ($request->getMethod() === Request::METHOD_DELETE) {
            $this->userRepository->removeFriend($user->id(), Uuid::from($request->get('friendId')));

            return new Response();
        }

        if ($request->getMethod() === Request::METHOD_POST) {
            try {
                $this->bus->dispatch(new AddFriendCommand($user->id()->value(), $request->get('friendName')));
            } catch (UserNotExistsException) {
                $error = $this->translator->trans('exception.user_not_exists');
            } catch (\Throwable $e) {
                $error = $e->getMessage();
            }
        }

        $friends = $this->userRepository->friends($user->id());

        return $this->render('user_friends.html.twig', [
            'friends' => $friends,
            'error' => $error,
        ]);
    }
}
