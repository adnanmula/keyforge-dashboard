<?php declare(strict_types=1);

namespace AdnanMula\Cards\Infrastructure\Security;

use Elastic\Apm\ElasticApm;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\ResponseEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\Routing\RouterInterface;

final readonly class ApmRouteSubscriber implements EventSubscriberInterface
{
    public function __construct(
        private RouterInterface $router,
    ) {
    }

    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::RESPONSE => ['onResponse', -255],
        ];
    }

    public function onResponse(ResponseEvent $event): void
    {
        if (false === $event->isMainRequest()) {
            return;
        }

        $request = $event->getRequest();

        $routeName = $request->attributes->get('_route');

        if (!is_string($routeName)) {
            return;
        }

        $route = $this->router
            ->getRouteCollection()
            ->get($routeName);

        if ($route === null) {
            return;
        }

        ElasticApm::getCurrentTransaction()->setName(sprintf(
            '%s %s',
            $request->getMethod(),
            $route->getPath(),
        ));
    }
}
