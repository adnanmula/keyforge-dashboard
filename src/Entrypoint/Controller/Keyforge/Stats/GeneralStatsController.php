<?php declare(strict_types=1);

namespace AdnanMula\Cards\Entrypoint\Controller\Keyforge\Stats;

use AdnanMula\Cards\Application\Query\Keyforge\Stats\GeneralStatsQuery;
use AdnanMula\Cards\Entrypoint\Controller\Shared\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

final class GeneralStatsController extends Controller
{
    public function __invoke(Request $request): Response
    {
        $data = $this->extractResult(
            $this->bus->dispatch(new GeneralStatsQuery()),
        );

        return $this->render('Keyforge/Stats/general_stats.html.twig', ['data' => $data],);
    }
}
