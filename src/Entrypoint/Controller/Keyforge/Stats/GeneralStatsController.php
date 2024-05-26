<?php declare(strict_types=1);

namespace AdnanMula\Cards\Entrypoint\Controller\Keyforge\Stats;

use AdnanMula\Cards\Application\Query\Keyforge\Stats\GeneralStatsQuery;
use AdnanMula\Cards\Application\Query\Keyforge\Stats\UserStatsQuery;
use AdnanMula\Cards\Domain\Model\Keyforge\Stat\KeyforgeStat;
use AdnanMula\Cards\Entrypoint\Controller\Shared\Controller;
use Symfony\Component\HttpFoundation\Response;

final class GeneralStatsController extends Controller
{
    public function __invoke(): Response
    {
        /** @var ?KeyforgeStat $data */
        $data = $this->extractResult($this->bus->dispatch(new GeneralStatsQuery()));

        $user = $this->getUser();
        $userData = null;

        if (null !== $user) {
            /** @var ?KeyforgeStat $userData */
            $userData = $this->extractResult($this->bus->dispatch(new UserStatsQuery($user->id()->value())));
        }

        return $this->render('Keyforge/Stats/general_stats.html.twig', [
            'data' => $data?->data,
            'userData' => $userData?->data,
        ]);
    }
}
