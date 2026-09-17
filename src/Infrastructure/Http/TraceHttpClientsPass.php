<?php declare(strict_types=1);

namespace AdnanMula\Cards\Infrastructure\Http;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Reference;

final class TraceHttpClientsPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container): void
    {
        $clientIds = ['dok.client'];

        foreach ($clientIds as $clientId) {
            if (false === $container->has($clientId)) {
                continue;
            }

            $decoratorId = $clientId . '.apm';

            $definition = new Definition(TracedHttpClient::class);
            $definition->setDecoratedService($clientId);
            $definition->setArgument('$inner', new Reference($decoratorId . '.inner'));
            $definition->setArgument('$clientId', $clientId);
            $container->setDefinition($decoratorId, $definition);
        }
    }
}
