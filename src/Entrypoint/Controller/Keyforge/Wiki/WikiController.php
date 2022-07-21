<?php declare(strict_types=1);

namespace AdnanMula\Cards\Entrypoint\Controller\Keyforge\Wiki;

use AdnanMula\Cards\Entrypoint\Controller\Shared\Controller;
use Symfony\Component\HttpFoundation\Response;

final class WikiController extends Controller
{
    public function __invoke(): Response
    {
        return $this->render('Keyforge/Wiki/wiki.html.twig');
    }
}
