<?php declare(strict_types=1);

namespace AdnanMula\Cards\Entrypoint\Controller\Keyforge\Stats;

use AdnanMula\Cards\Entrypoint\Controller\Shared\Controller;
use Symfony\Component\HttpFoundation\Response;

final class GeneralStatsController extends Controller
{
    public function __invoke(): Response
    {
        return $this->render('Keyforge/Stats/general_stats.html.twig');
    }
}
