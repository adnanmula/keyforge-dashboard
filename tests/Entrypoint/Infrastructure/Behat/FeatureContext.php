<?php declare(strict_types=1);

namespace AdnanMula\Cards\Tests\Entrypoint\Infrastructure\Behat;

use Behat\Behat\Context\Context;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\NullOutput;
use Symfony\Component\HttpKernel\KernelInterface;

final class FeatureContext implements Context
{
    private KernelInterface $kernel;

    public function __construct(KernelInterface $kernel)
    {
        $this->kernel = $kernel;
    }

    /** @Given the environment is clean */
    public function cleanEnvironment(): void
    {
        $this->bootstrapEnvironment();
    }

    /** @Given the environment with fixtures */
    public function loadFixtures(): void
    {
        $this->bootstrapEnvironment();
        $application = $this->getApplication();

        $arg = new ArrayInput(['command' => 'environment:fixtures']);

        $application->run($arg, new NullOutput());
    }

    private function bootstrapEnvironment(): void
    {
        $application = $this->getApplication();

        $application->run(new ArrayInput(
            [
                'command' => 'environment:database',
                '--no-interaction' => true,
            ],
        ), new NullOutput());

        $application->run(new ArrayInput(
            [
                'command' => 'environment:migrations',
                '--no-interaction' => true,
            ],
        ), new NullOutput());
    }

    private function getApplication(): Application
    {
        $app = new Application($this->kernel);
        $app->setAutoExit(false);

        return $app;
    }
}
