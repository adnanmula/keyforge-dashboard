<?php declare(strict_types=1);

namespace AdnanMula\Cards\Infrastructure\Security;

use AdnanMula\Cards\Entrypoint\Controller\Shared\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\Security\Http\Authorization\AccessDeniedHandlerInterface;

final class AccessDeniedHandler extends Controller implements AccessDeniedHandlerInterface
{
    public function handle(Request $request, AccessDeniedException $accessDeniedException): ?Response
    {
        return $this->render('/access_denied.html.twig', [], new Response(null, Response::HTTP_FORBIDDEN));
    }
}
