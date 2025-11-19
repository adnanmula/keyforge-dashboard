<?php declare(strict_types=1);

namespace AdnanMula\Cards\Infrastructure\Security;

use Symfony\Component\EventDispatcher\Attribute\AsEventListener;
use Symfony\Component\HttpKernel\Event\ResponseEvent;

#[AsEventListener(event: 'kernel.response', method: 'onKernelResponse')]
class SecurityHeadersListener
{
    public function onKernelResponse(ResponseEvent $event)
    {
        $response = $event->getResponse();

        $response->headers->set('X-Frame-Options', 'DENY');
        $response->headers->set('X-Content-Type-Options', 'nosniff');
        $response->headers->set('X-XSS-Protection', '1; mode=block');
        $response->headers->set('Referrer-Policy', 'no-referrer-when-downgrade');
    }
}
