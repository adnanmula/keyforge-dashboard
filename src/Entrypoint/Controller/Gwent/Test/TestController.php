<?php declare(strict_types=1);

namespace AdnanMula\Cards\Entrypoint\Controller\Gwent\Test;

use AdnanMula\Cards\Entrypoint\Controller\Shared\Controller;
use Symfony\Component\HttpFoundation\Response;

final class TestController extends Controller
{
    public function __invoke(): Response
    {
        return $this->render('Gwent/Shared/base.html.twig');
    }
}
