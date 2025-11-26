<?php declare(strict_types=1);

namespace AdnanMula\Cards\Infrastructure\Security;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\Event\ResponseEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\RateLimiter\RateLimiterFactory;

readonly final class RateLimitSubscriber implements EventSubscriberInterface
{
    public function __construct(
        private RateLimiterFactory $globalLimiter,
    ) {}

    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::REQUEST => ['onKernelRequest', 50],
            KernelEvents::RESPONSE => ['onKernelResponse', -50],
        ];
    }

    public function onKernelRequest(RequestEvent $event): void
    {
        if (false === $event->isMainRequest()) {
            return;
        }

        $request = $event->getRequest();

        $ip = $request->getClientIp() ?? 'unknown';

        $limiter = $this->globalLimiter->create($ip);
        $limit = $limiter->consume();

        $request->attributes->set('_rate_limit', $limit);

        if (false === $limit->isAccepted()) {
            $response = new Response(
                'Too Many Requests',
                Response::HTTP_TOO_MANY_REQUESTS,
                [
                    'Retry-After' => $limit->getRetryAfter()->format('Y-m-d H:i:s'),
                ],
            );

            $event->setResponse($response);
        }
    }

    public function onKernelResponse(ResponseEvent $event): void
    {
        if (false === $event->isMainRequest()) {
            return;
        }

        $request = $event->getRequest();
        $response = $event->getResponse();

        $limit = $request->attributes->get('_rate_limit');

        if (null === $limit) {
            return;
        }

        $response->headers->set('X-RateLimit-Limit', (string) $limit->getLimit());
        $response->headers->set('X-RateLimit-Remaining', (string) $limit->getRemainingTokens());

        if (false === $limit->isAccepted()) {
            $response->headers->set('Retry-After', $limit->getRetryAfter()?->format('Y-m-d H:i:s'));
        }
    }
}
