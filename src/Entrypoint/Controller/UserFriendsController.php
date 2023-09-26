<?php declare(strict_types=1);

namespace AdnanMula\Cards\Entrypoint\Controller;

use AdnanMula\Cards\Domain\Model\Shared\User;
use AdnanMula\Cards\Domain\Model\Shared\UserRepository;
use AdnanMula\Cards\Domain\Model\Shared\ValueObject\Uuid;
use AdnanMula\Cards\Entrypoint\Controller\Shared\Controller;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Translation\LocaleSwitcher;

final class UserFriendsController extends Controller
{
    public function __construct(
        private readonly UserRepository $userRepository,
        MessageBusInterface $bus,
        Security $security,
        LocaleSwitcher $localeSwitcher,
    ) {
        parent::__construct($bus, $security, $localeSwitcher);
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

        $friends = $this->userRepository->friends($user->id());

        return $this->render('user_friends.html.twig', [
            'friends' => $friends,
            'error' => $error,
        ]);
    }
}
