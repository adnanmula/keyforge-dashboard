<?php declare(strict_types=1);

namespace AdnanMula\Cards\Entrypoint\Controller\Keyforge\User;

use AdnanMula\Cards\Application\Query\Keyforge\Stats\UserStatsQuery;
use AdnanMula\Cards\Domain\Model\Shared\ValueObject\UserRole;
use AdnanMula\Cards\Entrypoint\Controller\Shared\Controller;
use Symfony\Component\HttpFoundation\Response;

final class UserDetailController extends Controller
{
    public function __invoke(string $userId): Response
    {
        $this->assertIsLogged(UserRole::ROLE_KEYFORGE);

        $stats = $this->extractResult($this->bus->dispatch(new UserStatsQuery($userId)))?->data;

        return $this->render(
            'Keyforge/User/user_detail.html.twig',
            [
                'reference' => $userId,
                'userId' => $userId,
                'stats' => $stats,
            ],
        );
    }
}
