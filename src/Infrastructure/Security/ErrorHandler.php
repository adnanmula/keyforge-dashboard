<?php declare(strict_types=1);

namespace AdnanMula\Cards\Infrastructure\Security;

use AdnanMula\Cards\Entrypoint\Controller\Shared\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

final class ErrorHandler extends Controller
{
    public function __invoke(Request $request): Response
    {
        return $this->render('Shared/error.html.twig', ['code' => 404], new Response(null, Response::HTTP_NOT_FOUND));
    }
}
