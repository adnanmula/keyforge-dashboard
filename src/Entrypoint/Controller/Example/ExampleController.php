<?php declare(strict_types=1);

namespace AdnanMula\Cards\Entrypoint\Controller\Example;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

final class ExampleController extends AbstractController
{
    public function __invoke(Request $request): Response
    {
        return $this->render('Example/example.html.twig');
    }
}
